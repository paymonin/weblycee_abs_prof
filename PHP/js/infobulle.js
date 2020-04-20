function GetId(id)
{
    return document.getElementById(id);
}
var display=false; // La variable i nous dit si la bulle est visible ou non

function move(e) {
    if(display) {  // Si la bulle est visible, on calcul en temps reel sa position ideale
        if (navigator.appName!="Microsoft Internet Explorer") { // Si on est pas sous IE
            GetId("curseur").style.left=e.pageX + 5+"px";
            GetId("curseur").style.top=e.pageY + 10+"px";
        }
        else {
            if(document.documentElement.clientWidth>0) {
                GetId("curseur").style.left=20+event.x+document.documentElement.scrollLeft+"px";
                GetId("curseur").style.top=10+event.y+document.documentElement.scrollTop+"px";
            } else {
                GetId("curseur").style.left=20+event.x+document.body.scrollLeft+"px";
                GetId("curseur").style.top=10+event.y+document.body.scrollTop+"px";
            }
        }
    }
}

function montre(text) {
    if(display==false) {
        GetId("curseur").style.visibility="visible"; // Si il est cacher (la verif n'est qu'une securité) on le rend visible.
        GetId("curseur").innerHTML = text; // on copie notre texte dans l'élément html
        display=true;
    }
}
function cache() {
    if(display==true) {
        GetId("curseur").style.visibility="hidden"; // Si la bulle est visible on la cache
        display=false;
    }
}
document.onmousemove=move; // dès que la souris bouge, on appelle la fonction move pour mettre à jour la position de la bulle.
//-->

