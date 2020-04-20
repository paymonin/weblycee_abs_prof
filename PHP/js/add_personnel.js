function add_personnel() {
    var add = document.getElementById("addPersonnel");
    add.innerHTML = "<td></td><td><input type='text' size='4' name='civilite' id='civilite'></td><td><input type='text' name='nom' id='nom'></td><td><input type='text' name='prenom' id='prenom'></td><td><input type='text' name='poste' id='poste'></td><td><input type='text' name='discipline' id='discipline'></td><td><input type='button' onclick='javascript:submit_personnel()' value='Ajouter'><input type='button' value='Annuler' onClick=\"javascript:window.location='personnel.php'\";></td>";

    addCompletion('discipline','discipline');
}

function submit_personnel() {
    var url = "personnel.php?action=3";
    url += "&civilite=" + (document.getElementById("civilite")).value;
    url += "&nom=" + (document.getElementById("nom")).value;
    url += "&prenom=" + (document.getElementById("prenom")).value;
    url += "&poste=" + (document.getElementById("poste")).value;
    url += "&discipline=" + (document.getElementById("discipline")).value;
    window.location=url;
}
