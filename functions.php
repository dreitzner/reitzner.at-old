<?php
define("pdoConnStr" , 'sqlite:src/data.db');

function dehtmlifyStr($string){
    $string = str_replace("&auml;", "ae", $string);
    $string = str_replace("&uuml;", "ue", $string);
    $string = str_replace("&ouml;", "oe", $string);
    $string = str_replace(" ", "", $string);
    return $string;
} 

function buildMenuItem($menuItem){
    $extern = $menuItem['extern'];
    $id = $menuItem['id'];
    $name = $menuItem['name'];
    if($extern == '') $link = "href='/$id/'" ;
    else $link = "href='$extern' target='_blank'";
    return "<a $link id='$id'>$name</a>";
}

function getMenu(){
    try{
        $db = new PDO(pdoConnStr);

        #generate menu
        $result = $db->query('SELECT name,flow,extern FROM site_names INNER JOIN site_menu ON site_names.id = site_menu.id ORDER BY flow');
        $menuArr = [];
        $sub = false;
        while ($res = $result->fetch()) {
            $tempArr = [];
            $tempArr['name']=$res['name'];
            $order= intval($res['flow']);
            $tempArr['extern']=$res['extern'];
            $tempArr['id']= dehtmlifyStr($res['name']);
            $menuArr[$order][] = $tempArr;
        }
        $menu = "<ul class='menu'>";
        foreach($menuArr as $menuItem){
            if(count($menuItem) == 1){
                $menu .= "<li>".buildMenuItem($menuItem[0])."</li>";
            }else{
                $menu .= "<li class='sub'>".buildMenuItem($menuItem[0])."<ul>";
                for($i = 1; $i < count($menuItem) ;$i++){
                    $menu .= "<li>".buildMenuItem($menuItem[$i])."</li>";
                }
                $menu .= "</ul></li>";
            }
        }
        $menu .= "</ul>";
        $db=0;
        return $menu;
    }
    catch(Exception $err){
        return $err;	
    }
}

function getContent($title){
    try{
        $db = new PDO(pdoConnStr);

        #get content
        $result = $db->query('SELECT name, id FROM site_names');
        while($res = $result->fetch() ) {
            $string = deHtmlifyStr($res['name']);
            if(strtolower($string) == strtolower($title) ){
                $id = $res['id'];
                $result = $db->query('SELECT * FROM site_content WHERE id ='.$id);
                $res = $result->fetch();
                $db=0;
                return $res[1];
            }
        }
        $db=0;
        return "<h1>Dieser Inhalt ist nicht mehr Verf&uuml;gbar</h1><p>Da scheint was schief gelaufen zu sein...<br>&Uuml;ber das Men&uuml; findest deinen Weg wieder zur&uuml;ck.</p>";
    }
    catch(Exception $err){
        return $err;	
    }
}