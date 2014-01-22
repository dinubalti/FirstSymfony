function createLoginPanel() {   
    new Ext.form.FormPanel({
        title: 'Logare',
        bodyStyle : 'padding:5px 5px 0',
        labelWidth: 75,
        width: 400,
        defaults : {           
            anchor: '95%'
        },
        autoScroll : true,
        frame: true,
        renderTo : 'login-form',
        items : [
            {
                xtype : 'textfield',
                id: 'loginField',
                fieldLabel: 'Login',
                labelSeparator : ' *:',
                listeners: {
                    specialkey: function(f,e){
                        if (e.getKey() == e.ENTER) {
                            login();
                        }
                    }
                }
            },{
                xtype : 'textfield',
                id: 'pwdField',
                fieldLabel: 'Parola',               
                labelSeparator : ' *:',
                inputType: 'password',
                listeners: {
                    specialkey: function(f,e){
                        if (e.getKey() == e.ENTER) {
                            login();
                        }
                    }
                }
            }
        ],
        buttons: [{
            icon : '../public/images/login.png',
            text : 'Conectare',
            style : 'margin:10px',
            handler: login
        }]
    });
}

function login() {
    var validationMessage = '';
    
    if (isBlank(Ext.getCmp('loginField').getValue())) {
        validationMessage += 'Login-ul este gol!' + '</br>';
    }
    if (isBlank(Ext.getCmp('pwdField').getValue())) {
        validationMessage += 'Parola este goala!' + '</br>';
    }
    
    if (!Ext.isEmpty(validationMessage)) {
       Ext.Msg.show({
            title : 'Date invalide',
            msg : validationMessage,
            buttons : Ext.Msg.OK,
            icon : Ext.MessageBox.ERROR
       }); 
    } else {
        Ext.Ajax.request({
            url: Routing.generate('login_login_action'),
            success: loginCallback,
            params: { 
                login: Ext.getCmp('loginField').getValue(),
                password: Ext.getCmp('pwdField').getValue()
            }
        });
    }
}

function loginCallback(resultJSON) {
    if (!errorResultObj(resultJSON)) {
        document.location.href = 'home';
    }
}

Ext.onReady(function() {
    Ext.QuickTips.init();
    
    createLoginPanel(); 
});