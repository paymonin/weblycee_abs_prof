function setSelected(id, value)
{
    for(i=0;i<document.getElementById(id).length;i++)
        if(document.getElementById(id).options[i].value==value)
            document.getElementById(id).selectedIndex=i;
}

function setValue(id, value)
{
	document.getElementById(id).value=value;
}
