jQuery(document).ready(function ()
{
    getLatestNews();
});

function onBtnCheckLatestVersionClick(sender, event)
{
    var resultDiv = document.getElementById('survfLatestVersion');

    resultDiv.innerHTML = '<img src="components/com_surveyforce/assets/images/ajax_loader_16x11.gif" />';

    var url = 'index.php?option=com_surveyforce&task=general.get_latest_component_version';
    var xmlData = "";
    var syncObject = {};
    var timeout = 5000;
    var dataCallback = function(request, syncObject, responseText) { onGetLatestVersionData(request, syncObject, responseText); };
    var timeoutCallback = function(request, syncObject) { onGetLatestVersionTimeout(request, syncObject); };

    MyAjax.makeRequest(url, xmlData, syncObject, timeout, dataCallback, timeoutCallback);
}

function onGetLatestVersionData(request, syncObject, responseText)
{
    var resultDiv = document.getElementById('survfLatestVersion');

    // Handling XML.

    var xmlDoc = MethodsForXml.getXmlDocFromString(responseText);
    var rootNode = xmlDoc.documentElement;

    var error = MethodsForXml.getNodeValue(rootNode.childNodes[0]);
    var status = MethodsForXml.getNodeValue(rootNode.childNodes[1]);
    var version = MethodsForXml.getNodeValue(rootNode.childNodes[2]);
    var changelog = MethodsForXml.getNodeValue(rootNode.childNodes[3]);
    var link = MethodsForXml.getNodeValue(rootNode.childNodes[4]);

    // Handling data.

    if (error == "" && status == 200)
    {
        if (version == currentVersion)
        {
            resultDiv.innerHTML = '<span style="color: #008000">' + version + '</span>';
        }
        else
        {
            jQuery('#body').html('<h3>Version: ' + version + '</h3>' +
                changelog +
                '<br/><hr/>If you want to see full list of component changes, please follow this link: ' + '<a href="' + link + '" target="_blank">' + Joomla.JText._('COM_SURVEYFORCE_BE_CONTROL_PANEL_CHANGELOG') + '</a>');
            jQuery('#changelogModal').modal('show');
            resultDiv.innerHTML = '<button class="btn btn-small" onclick="onBtnCheckLatestVersionClick(this, event);"><i class="icon-health"></i>' + Joomla.JText._('COM_SURVEYFORCE_BE_CONTROL_PANEL_BUT_CHECK_VERSION') + '</button>';
        }
    }
    else
    {
        resultDiv.innerHTML = '<span style="color: red">' + Joomla.JText._('COM_SURVEYFORCE_BE_CONTROL_PANEL_CONNECTION_FAILED') + error + (error == '' ? '' : ', ') +
            (status == -100 ? Joomla.JText._('COM_SURVEYFORCE_BE_CONTROL_PANEL_TIMEOUT') : status) + '</span>';
    }
}

function onGetLatestVersionTimeout(request, syncObject)
{
    var resultDiv = document.getElementById('survfLatestVersion');

    resultDiv.innerHTML = '<span style="color: red">' + Joomla.JText._('COM_SURVEYFORCE_BE_CONTROL_PANEL_CONNECTION_FAILED') + ': ' +
        Joomla.JText._('COM_SURVEYFORCE_BE_CONTROL_PANEL_TIMEOUT') + '</span>';
}

function getLatestNews()
{
    var url = 'index.php?option=com_surveyforce&task=general.get_latest_news';
    var xmlData = "";
    var syncObject = {};
    var timeout = 5000;
    var dataCallback = function(request, syncObject, responseText) { onGetLatestNewsData(request, syncObject, responseText); };
    var timeoutCallback = function(request, syncObject) { onGetLatestNewsTimeout(request, syncObject); };

    MyAjax.makeRequest(url, xmlData, syncObject, timeout, dataCallback, timeoutCallback);
}

function onGetLatestNewsData(request, syncObject, responseText)
{
    var resultDiv = document.getElementById('survfLatestNews');

    // Handling XML.

    var xmlDoc = MethodsForXml.getXmlDocFromString(responseText);
    var rootNode = xmlDoc.documentElement;

    var error = MethodsForXml.getNodeValue(rootNode.childNodes[0]);
    var status = MethodsForXml.getNodeValue(rootNode.childNodes[1]);
    var content = MethodsForXml.getNodeValue(rootNode.childNodes[2]);

    // Handling data.

    if (error == "" && status == 200)
    {
        resultDiv.innerHTML = content;
    }
    else
    {
        resultDiv.innerHTML = '<span style="color: red">' + Joomla.JText._('COM_SURVEYFORCE_BE_CONTROL_PANEL_CONNECTION_FAILED') + ': ' +
            Joomla.JText._('COM_SURVEYFORCE_BE_CONTROL_PANEL_TIMEOUT') + '</span>';
    }
}

function onGetLatestNewsTimeout(request, syncObject)
{
    var resultDiv = document.getElementById('survfLatestNews');

    resultDiv.innerHTML = '<span style="color: red">' + Joomla.JText._('COM_SURVEYFORCE_BE_CONTROL_PANEL_CONNECTION_FAILED') + ': ' +
        Joomla.JText._('COM_SURVEYFORCE_BE_CONTROL_PANEL_TIMEOUT') + '</span>';
}

function onBtnShowChangelogClick(sender, event)
{
    var link = 'index.php?option=com_surveyforce&task=general.show_changelog&tmpl=component';
    var width = 620;
    var height = 620;

    var linkElement = document.createElement('a');
    linkElement.href = link;

    SqueezeBox.fromElement(linkElement, { handler: 'iframe', size: { x: width, y: height }, url: link });
}

function dateAjaxRef()
{
    jQuery.ajax({
        type: "POST",
        url: "index.php?option=com_surveyforce&task=general.datedb"
    });
    window.open("http://www.joomplace.com/support/product-improvement-survey?usf_source=SurveyForce&usf_medium=Survey&usf_campaign=Product%2BImprovement", "_blank");
}

function dateAjaxIcon()
{
    jQuery.ajax({
        type: "POST",
        url: "index.php?option=com_surveyforce&task=general.datedb"
    });
    jQuery('#notification').remove();
}
