var fp = null;

function createLoginPanel() {   
    fp = new Ext.form.FormPanel({
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
                    render: function(c) {
                        c.getEl().on('keyup', function() {
                          enableRegistrationButton();
                        }, c);
                    }
                },
                autoCreate : {
                    tag : 'input', 
                    maxlength : 10
                },
                vtype : 'loginValid'
            },{
                xtype : 'textfield',
                id: 'pwdField',
                fieldLabel: 'Parola',               
                labelSeparator : ' *:',
                inputType: 'password',
                listeners: {
                    render: function(c) {
                        c.getEl().on('keyup', function() {
                          enableRegistrationButton();
                        }, c);
                    }
                },
                vtype : 'passValid'
            },{
                xtype : 'textfield',
                id: 'pwdRepeatField',
                fieldLabel: 'Parola (repetati)',               
                labelSeparator : ' *:',
                inputType: 'password',
                listeners: {
                    render: function(c) {
                        c.getEl().on('keyup', function() {
                          enableRegistrationButton();
                        }, c);
                    }
                },
                vtype : 'passValid'
            },{
                xtype : 'textfield',
                id: 'siteField',
                fieldLabel: 'Web site',               
                labelSeparator : ' *:',
                listeners: {
                    render: function(c) {
                        c.getEl().on('keyup', function() {
                          enableRegistrationButton();
                        }, c);
                    }
                },
                vtype : 'siteValid'
            },{
                xtype      : 'checkbox',
                id: 'publicCheckBox',
                fieldLabel : "",
                boxLabel   : 'Cu cererea publica cunoscut',
                inputValue : 'a',
                handler : enableRegistrationButton
              }
        ],
        buttons: [{
            id: 'registrationButton',
            icon : '../public/images/login.png',
            text : 'Inregistrare',
            style : 'margin:10px',
            handler: login,
            disabled: true
        }]
    });
}

function enableRegistrationButton() {
    
    if(fp.getForm().isValid() && 
            !isBlank(Ext.getCmp('loginField').getValue()) && 
            !isBlank(Ext.getCmp('pwdField').getValue())  && 
            !isBlank(Ext.getCmp('pwdRepeatField').getValue()) && 
            !isBlank(Ext.getCmp('siteField').getValue()) &&
            Ext.getCmp('publicCheckBox').checked == true){
        Ext.getCmp('registrationButton').enable();
    } else {
        Ext.getCmp('registrationButton').disable();
    }
}

function login() {
    var validationMessage = '';
    
    if (Ext.getCmp('pwdField').getValue() !== Ext.getCmp('pwdRepeatField').getValue()) {
        validationMessage += 'Parolele nu coincid';
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
            url: Routing.generate('examen_login_action'),
            success: loginCallback,
            params: { 
                login: Ext.getCmp('loginField').getValue(),
                password: Ext.getCmp('pwdField').getValue(),
                website: Ext.getCmp('siteField').getValue()
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