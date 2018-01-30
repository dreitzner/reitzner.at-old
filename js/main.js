// GLOBAL vars
let menu = "";
let windowWidth = 0;

function changeURL(id) {
    if (menu != id) {
        menu = id;
        let permalink = `../${id}/`;
        history.pushState({ page: id }, `reitzner.at - ${id}`, permalink);
        getContent();
    }else{
        mobileNav();
    }
}

function mobileNav() {
    document
        .querySelector("nav ul")
        .classList
        .toggle("mobNavShow");
    document
        .querySelector('#overlay')
        .classList
        .toggle("show");
}

/**
 * Sets the global veriable with where we are
 */
function getLocation() {
    menu = location.pathname.replace(/\//g, '');
}

function setActiveMenu() {
    let active = document.querySelector('.active');
    if(active) active.classList.toggle('active');
    document
        .querySelector(`#${menu}`)
        .parentElement
        .classList
        .toggle('active');
}

function getContent() {
    // change data
    fetch(`/getData.php?con=${menu}`)
        .then(fetchErrors)
        .then(fetchParseJSON)
        .then(fetchUpdatePage)
        .catch(fetchShowErrors);

    // change header img
    let imgUrl = `/img/header/${menu}.jpg`;
    fetch(`/getData.php?media=${encodeURIComponent(imgUrl)}`)
        .then(fetchErrors)
        .then(fetchParseJSON)
        .then(fetchUpdateHeaderImg)
        .catch(fetchShowErrors);

    setActiveMenu();
    //for mobile
    let overlayVisible = document
                            .querySelector("#overlay")
                            .classList
                            .contains("show");
    if (windowWidth < 1024 && overlayVisible) {
        mobileNav();
    }
}

/**
 * Checks if response is OK
 * @param {Object} response 
 */
function fetchErrors(response) {
    if (!response.ok) {
        throw Error(response.status);
    }
    return response;
}

/**
 * Takes in an OK response and parses it as json
 * @param {Object} response 
 */
function fetchParseJSON(response){
    return response.json();
}

/**
 * Takes in the response json and makes the necessary changes to the page.
 * @param {{content: string}} json 
 */
function fetchUpdatePage(json) {
    // create string template
    let title = `reitzner.at - ${menu}`;

    // fill in the new page and page title
    document
        .querySelector('section')
        .innerHTML = json.content;

    document
        .querySelector('#title')
        .innerHTML = title;

    document.title = title;
}

/**
 * Takes in the response json and looks if the header Image exists
 * When it does we'll change it out.
 * @param {{exists: boolean, url: string}} json 
 */
function fetchUpdateHeaderImg(json){
    let img = document.querySelector("header img");
    let src = (json.exists === "true") ? "/"+json.url : "/img/header/Home.jpg";
    img.src = src;
    img.alt = menu;
}

/**
 * General Error Handling for all fetch requests
 * @param {*} err 
 */
function fetchShowErrors(err) {
    console.log(err);
    document
        .querySelector('main')
        .innerHTML = "<h1>Es ist ein unerwarteter Fehler aufgetreten.</h1><br/>" + err;
}

window.onload = () => {
    // Check if path is blank
    getLocation();
    if (menu == "") {
        menu = "Home";
        history.pushState({ page: "Home" }, "reitzner.at - Home", "/Home/");
    }
    setActiveMenu();
    // Bind Menu Items & wappen click
    let menuItems = document.querySelectorAll("nav a");
    menuItems.forEach( (el,ind,obj) => {
        if(!el.attributes.target){
            el.addEventListener("click", (ev) => {
                ev.preventDefault()
                changeURL(el.id);
            });
        }
    });
    document
        .querySelector("#wappen")
        .addEventListener("click", (ev) => changeURL("wappen") );

    // bind hide show menu
    document
        .querySelector("#mobileMenu")
        .addEventListener("click", () => mobileNav() );

    // bind kill overlay and menu
    document
        .querySelector("#overlay")
        .addEventListener("click", () => mobileNav() );

    // bind browser navigation
    window.onpopstate = ev => {
        getLocation();
        let state = ev.originalEvent.state;
        if (state) getContent();
        else window.history.back();
    }

    //bind resize so we don't have to check all the time
    window.onresize = (event) => {
        windowWidth = window.innerWidth;
    };
};