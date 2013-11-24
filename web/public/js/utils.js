function errorResultObj(resultJSON) {
    var resultObj = Ext.decode(resultJSON.responseText);
    if (resultObj.result === 'ERROR') {
        Ext.Msg.show({
            title: 'Eroare',
            icon: Ext.Msg.ERROR,
            msg: resultObj.message,
            buttons: Ext.Msg.OK
        });
    } else if(resultObj.result === 'WARNING') {
        Ext.Msg.show({
            title: 'Avertizare',
            icon: Ext.Msg.WARNING,
            msg: resultObj.message,
            buttons: Ext.Msg.OK
        });
    } else {
        return false;
    }
    return true;
}

function getEmailRegex() {
    return /^([\w\!\#$\%\&\'\*\+\-\/\=\?\^\`{\|\}\~]+\.)*[\w\!\#$\%\&\'\*\+\-\/\=\?\^\`{\|\}\~]+@((((([a-zA-Z0-9]{1}[a-zA-Z0-9\-]{0,62}[a-zA-Z0-9]{1})|[a-zA-Z])\.)+[a-zA-Z]{2,6})|(\d{1,3}\.){3}\d{1,3}(\:\d{1,5})?)$/
}

function isBlank(value){
	return (Ext.isEmpty(value) || Ext.util.Format.trim(value).length === 0);
};
