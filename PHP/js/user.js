function edit_user_level( username, password, usertype, txt,id) 
	{
	if ((document.getElementById("statut").innerHTML)=="modification en cours")
		{
		alert("validez la ligne avant de continuer!");
		}
	else
		{
		
		document.getElementById("statut").innerHTML = "modification en cours";
    			
		var select ="<select id='usertype'>";
		select = select+"<option value='1'";
		if (usertype==1) { select = select + " selected='' "}
		select = select+">administrateur </option>";
		select = select+"<option value='2'";
		if (usertype==2) { select = select + " selected='' "}
		select = select+">pré-saisie </option>";
		select = select+"<option value='3'";
		if (usertype==3) { select = select + " selected='' "}
		select = select+">consultation </option>";
		select = select+"</select>";
		document.getElementById("usertype"+id).innerHTML = select;
						
		var val_button = "<input type='button' value='Valider' onClick=\"javascript:window.location=update_user('"+username+"','"+password+"',document.getElementById(\'usertype\').value,'"+txt+"')\";>";
		var cancel_button = "<input type='button' value='Annuler' onClick=\"javascript:window.location='users.php'\";>";
		document.getElementById("save"+id).innerHTML = val_button + cancel_button;
		}
	}	

function edit_user_password( username, password, usertype, txt, id) 
	{
	if ((document.getElementById("statut").innerHTML)=="modification en cours")
		{
		alert("validez la ligne avant de continuer!");
		}
	else
		{
		document.getElementById("statut").innerHTML = "modification en cours";
    	
		var pass="<input type='password' size='30' value='' id='passwordA' >";
		pass = pass+"<input type='password' size='30' value='' id='passwordB' >";
		pass = pass+"<input type='hidden' size='30' value='"+password+"' id='password'>";
		
		
		document.getElementById("password"+id).innerHTML = pass;
		
		document.getElementById("usertype"+id).innerHTML = "<input type=hidden value='"+usertype+"'>";
				
		var val_button = "<input type='button' value='Valider' ";
		val_button = val_button + "onClick=\"javascript:if (password_validate() ) window.location=update_user('"+username+"',document.getElementById('password').value,'"+usertype+"','"+txt+"')\">";
		var cancel_button = "<input type='button' value='Annuler' onClick=\"javascript:window.location='users.php'\";>";
		document.getElementById("save"+id).innerHTML = val_button + cancel_button;
		
		}
	}	
	
function edit_user_txt( username, password, usertype, txt, id) 
	{
	if ((document.getElementById("statut").innerHTML)=="modification en cours")
		{
		alert("validez la ligne avant de continuer!");
		}
	else
		{
		document.getElementById("statut").innerHTML = "modification en cours";
    	
		var txt="<input type='text' size='120' value='' id='usertxt'>";
				
		document.getElementById("usertxt"+id).innerHTML = txt;
		
		var val_button = "<input type='button' value='Valider' ";
		val_button = val_button + "onClick=\"javascript:window.location=update_user('"+username+"','"+password+"','"+usertype+"',document.getElementById('usertxt').value)\">";
		var cancel_button = "<input type='button' value='Annuler' onClick=\"javascript:window.location='users.php'\";>";
		document.getElementById("save"+id).innerHTML = val_button + cancel_button;
		
		}
	}		
	
	
function update_user(username,password,usertype,txt)
{
    return "users.php?action=2&username="+username+"&password="+password+"&usertype="+usertype+"&txt="+txt;
	document.getElementById("statut").innerHTML = "";
		
}
	
function add_user() {
    var add = document.getElementById("adduser");
    
	
	var select ="<select id='usertype'>";
	select = select+"<option value='1'>administrateur </option>";
	select = select+"<option value='2'>pré-saisie </option>";
	select = select+"<option value='3'>consultation </option>";
	select = select+"</select>";
	
	var pass="<input type='password' size='30' value='' id='passwordA'>";
	pass = pass+"<input type='password' size='30' value='' id='passwordB' onChange=\"javascript:password_validate()\">";
	pass = pass+"<input type='hidden' size='30' id='password'>";
		
	var txt="<input type='text' size='120' value='' id='usertxt'>";
	
	
	add.innerHTML = "<td></td><td><input type='text' size='20' name='username' id='username'></td><td>"+pass+"</td><td>"+select+"</td><td>"+txt+"</td><td><input type='button' onclick='javascript:submit_user()' value='Ajouter'><input type='button' value='Annuler' onClick=\"javascript:window.location='users.php'\";></td>";
}	
	

function submit_user() {
    var url = "users.php?action=3";
    url += "&username=" + (document.getElementById("username")).value;
    url += "&password=" + (document.getElementById("password")).value;
    url += "&usertype=" + (document.getElementById("usertype")).value;
	url += "&usertxt=" + (document.getElementById("usertxt")).value;
    window.location=url;
}

function password_validate() {

    var passA = document.getElementById("passwordA").value;
	var passB = document.getElementById("passwordB").value;
	
	if (passA===passB) {	
		document.getElementById("password").value = MD5(passB);
		return true;
	}
	else {
		alert("les deux mots de passe saisis sont différents !");
		document.getElementById("passwordA").value = "";
		document.getElementById("passwordB").value = "";
		document.getElementById("passwordA").focus();
		return false;
	}
	
}	
