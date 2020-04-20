function edit_personnel(type, t0, t1, t2, t3, t4, j, id) 
	{
	if ((document.getElementById("statut").innerHTML)=="modification en cours")
		{
		alert("validez la ligne avant de continuer!");
		}
	else
		{
		document.getElementById("statut").innerHTML = "modification en cours";
    	document.getElementById("civi"+j).innerHTML = "<input type='text' size='4' value='"+t0+"' id=civi_"+j+">";
		document.getElementById("nom"+j).innerHTML = "<input type='text' size='30' value='"+t1+"' id=nom_"+j+">";
		document.getElementById("prenom"+j).innerHTML = "<input type='text' size='19' value='"+t2+"' id=prenom_"+j+">";
		document.getElementById("poste"+j).innerHTML = "<input type='text' size='24' value='"+t3+"' id=poste_"+j+">";
		document.getElementById("discipline"+j).innerHTML = "<input size='30' type='text' value='"+t4+"' id=discipline_"+j+">";
		document.getElementById("save"+j).innerHTML = "<input type='button' value='Valider' onClick=\"javascript:window.location=update_personnel(document.getElementById(\'civi_"+j+"\').value, document.getElementById(\'nom_"+j+"\').value, document.getElementById(\'prenom_"+j+"\').value, document.getElementById(\'poste_"+j+"\').value, document.getElementById(\'discipline_"+j+"\').value, "+id+")\";>"+"<input type='button' value='Annuler' onClick=\"javascript:window.location='personnel.php'\";>";
		}
	}	

function update_personnel(c, n, p, po, d, id)
{
    return "personnel.php?action=2&civi="+c+"&nom="+n+"&prenom="+p+"&poste="+po+"&discipline="+d+"&id="+id;
	document.getElementById("statut").innerHTML = "";
}
