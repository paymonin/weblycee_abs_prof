function add_lieu() {
    var add = document.getElementById("addLieu");
    add.innerHTML = "<td></td><td><input type='text' name='nom' id='nom'></td><td><input type='text' name='adresse' id='adresse'></td><td><input type='button' onclick='javascript:submit_lieu()' value='Ajouter'></td>";

    addCompletion('discipline','discipline');
}

function submit_lieu() {
    var url = "lieux.php?action=3";
    url += "&nom=" + (document.getElementById("nom")).value;
    url += "&adresse=" + (document.getElementById("adresse")).value;
    window.location=url;
}
