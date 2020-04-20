function addCompletion(id, list)
{
    var options_xml =
    {
        script:"xml.php?",
        varname:list,
		maxresults: "5",
		timeout: "5000"
    };
var as_xml = new bsn.AutoSuggest(id, options_xml);
}
