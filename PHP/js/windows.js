function closePopup()
{
    url = window.opener.location;
    window.opener.location = url;
    window.close();
}
