function createSendEmailPanel() {
    var userProxy = new Ext.data.HttpProxy({
        url: Routing.generate('send_email_user_list')
    });
    
    var userStore = new Ext.data.JsonStore({
        id: 'userStore',
        proxy: userProxy,
        idProperty: 'id',
        root: 'data',
        baseParams: {
            userId: document.getElementById('userId').value
        },
        exceptionHandler : exceptionHandler,
        fields: [
            {name: 'id', type: Ext.data.Types.NUMBER},
            {name: 'label', type: Ext.data.Types.STRING},
            {name: 'email', type: Ext.data.Types.STRING}
        ],
        listeners : {
            exception : exceptionHandler
	}
    });
    userStore.load();
    
    new Ext.form.FormPanel({
        title: 'Trimite email',
        bodyStyle : 'padding:5px 5px 0',
        labelWidth: 75,
        defaults : {           
            anchor: '95%'
        },
        autoScroll : true,
        frame: true,
        renderTo : 'send-email-panel',
        items : [
            {
                xtype : 'combo',
                fieldLabel : 'Catre',
                labelSeparator : ' *:',
                id : 'userField',
                valueField : 'id',
                displayField : 'label',
                store : userStore,
                triggerAction : 'all',
                editable : false,
                mode : 'local',
                tpl : '<tpl for="."><div class="x-combo-list-item">&nbsp;{label:htmlEncode}</div></tpl>'
            },
            {
                xtype : 'textfield',
                id: 'subjectField',
                fieldLabel: 'Subiect',
                labelSeparator : ' :',
            },{
                xtype : 'htmleditor',
                id: 'contentField',
                fieldLabel: 'Continut',               
                labelSeparator : ' :'
            }
        ],
        buttons: [{
            icon : '../public/images/mail_send.png',
            text : 'Trimite',
            handler : sendEmail,
            style : 'margin:10px'
        },{
            icon : '../public/images/button_reset.png',
            text : 'Anuleaza',
            handler : resetFields,
            style : 'margin:10px'
        }]
    });
}

function sendEmail() {
    var userCombo = Ext.getCmp('userField');
    if (Ext.isEmpty(userCombo.getValue())) {
        Ext.Msg.show({
            title : 'Date invalide',
            msg : 'Alegeti destinatarul!',
            buttons : Ext.Msg.OK,
            icon : Ext.MessageBox.ERROR
       });
    } else {
        var userIndexRecord = userCombo.store.findExact('id' , userCombo.getValue());
        var userRecord = userCombo.store.getAt(userIndexRecord);
        var to = userRecord.data.email;

        Ext.Msg.show({
            title : 'Confirmare trimitere',
            msg : 'Sunteti sigur ca trimiteti acest email?',
            buttons : Ext.Msg.YESNO,
            icon : Ext.MessageBox.QUESTION,
            fn : function(btn) {
                if (btn == 'yes') {
                    Ext.Ajax.request({
                        url: Routing.generate('send_email_send'),
                        success: sendEmailCallback,
                        params: { 
                            to: to,
                            subject: Ext.getCmp('subjectField').getValue(),
                            content: Ext.getCmp('contentField').getValue()
                        }
                    });
                }
            }
        });
    }
}

function sendEmailCallback(resultJSON){
    if (!errorResultObj(resultJSON)) {
        Ext.Msg.show({
            title : 'Success',
            msg : 'Mesajul a fost trimis cu succes.',
            buttons : Ext.Msg.OK,
            icon : Ext.MessageBox.INFO
       });
    }
}

function resetFields() {
    Ext.getCmp('userField').setValue('');
    Ext.getCmp('subjectField').setValue('');
    Ext.getCmp('contentField').setValue('');
}

Ext.onReady(function() {
    Ext.QuickTips.init();
    
    createSendEmailPanel(); 
});