// GLOBAL vars
let menu = "";

function changeURL(id) {
    menu = id;
    let permalink = `../${id}/`;
    history.pushState({page: id}, `reitzner.at - ${id}`, permalink);
    getContent();
}

function mobileNav() {
    $("nav ul").toggleClass("mobNavShow");
    $('#overlay').toggle();
}

function getLocation(){
    menu = $(location).attr('pathname').replace(/\//g, '');
}

function setActiveMenu(){
    $('.active').toggleClass('active');
    $("#" + menu).parent().toggleClass('active');
}

function getContent() {
    $.get("/getData.php?con=" + menu)
        .done((data) => {
            $('section').html(data);
            let title = "reitzner.at - " + menu;
            let headerImgSrc = `/img/header/${menu}.jpg`;
            $(document).find("title").text(title);
            $('#title').text(title);
            $('header img').attr("src",headerImgSrc);
            $.get("/getData.php?media=" + encodeURIComponent(headerImgSrc) )
                .done(data => {
                    if(data != "true") $('header img').attr("src","/img/header/Home.jpg");
                    else $('header img').attr("src",headerImgSrc);
                })
                .fail(err => console.log(err));
            $('header img').attr("alt",menu);
        })
        .fail((err) => {
            $('main').html("<h1>Es ist ein unerwarteter Fehler aufgetreten.</h1><br/>" + err.message);
        });
    setActiveMenu();
    //for mobile
    if ($(window).width() < 1024 && $("#overlay").is(":visible")) {
        mobileNav();
    }
}

window.onload = () => {
    // Check if path is blank
    getLocation();
    if (menu == "") {
        menu = "Home";
        history.pushState({page: "Home"}, "reitzner.at - Home", "/Home/");
    }
    setActiveMenu();
    // Bind Menu click
    $("nav a, #wappen").click(function (ev) {
        if($(this).attr("target") !== "_blank"){
            ev.preventDefault()
            changeURL($(this).attr("id"))
        }
    });
    // bind hide show menu
    $("#mobileMenu").click(() => mobileNav());
    // bind kill overlay and menu
    $("#overlay").click(() => mobileNav());
    // bind browser navigation
    $(window).on('popstate', ev => {
        getLocation();
        let state = ev.originalEvent.state;
        if (state)  getContent();
        else window.history.back();
    });
};