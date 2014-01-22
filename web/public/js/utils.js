Ext.apply(Ext.form.VTypes, {
    notBlank : function(val, field) {
        return !isBlank(val);
    },
    notBlankText : 'Campul este gol.',
    
    emailValid : function(val, field) {
        var regex = getEmailRegex();
	return regex.test(val);
    },
    emailValidText : 'Email invalid.',
    
    telNumber: function(val, field){
        var telNumberRegex = /^\d{1}\(\d{3}\)\d{1}\-\d{2}\-\d{2}$/;
        return telNumberRegex.test(val);
    },
    telNumberText: 'Telefonul trebuie sa respecte sablonul 9(999)9-99-99.',
    
    loginValid: function(val, field){
        var loginRegex = /^[a-zA-Z_]{4,10}$/;
        return loginRegex.test(val);
    },
    loginValidText: 'Login-ul trebuie sa fie alcatuit doar din litere engleze si _. Si trebuie sa fie mai mare de 4 caractere.',
    
    passValid: function(val, field){
        var passRegex = /^[a-zA-Z0-9?!.]{3,}(\d{1,})$/;
        return passRegex.test(val);
    },
    passValidText: 'Parola trebuie sa contina numai litere engleze, macar o cifra si semn de punctuatie. Lungimea minima - 4.',
    
    siteValid: function(val, field){
        var siteRegex = /^(https|http|ftp)\:\/\/|([a-z0-9A-Z]+\.[a-z0-9A-Z]+\.[a-zA-Z]{2,4})|([a-z0-9A-Z]+\.[a-zA-Z]{2,4})|\?([a-zA-Z0-9]+[\&\=\#a-z]+)/i;
        return siteRegex.test(val);
    },
    siteValidText: 'Site invalid'

});

Ext.form.Field.prototype.msgTarget = 'side';

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
}

function exceptionHandler(ex) {
   Ext.Msg.show({
           title : 'Eroare',
           msg : 'S-a produs o eroare tehnica!',
           buttons : Ext.Msg.OK,
           icon : Ext.MessageBox.ERROR
   });
}

function escapeHtml(value) {
    return Ext.util.Format.htmlEncode(value);
}
