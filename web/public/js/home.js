var winChooseGroups = null;
var groupIds = [];

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
    telNumberText: 'Telefonul trebuie sa respecte sablonul 9(999)9-99-99.'
});

function createUserGrid(){
    var proxy = new Ext.data.HttpProxy({
        url: Routing.generate('user_list')
    });

    var store = new Ext.data.JsonStore({
        id: 'userStore',
        proxy: proxy,
        idProperty: 'id',
        root: 'data',
        totalProperty: 'totalCount',
        remoteSort: true,
        exceptionHandler : exceptionHandler,
        fields: [
            {name: 'id', type: Ext.data.Types.NUMBER},
            {name: 'firstName', type: Ext.data.Types.STRING},
            {name: 'secondName', type: Ext.data.Types.STRING},
            {name: 'login', type: Ext.data.Types.STRING},
            {name: 'password', type: Ext.data.Types.STRING},
            {name: 'email', type: Ext.data.Types.STRING},
            {name: 'phone', type: Ext.data.Types.STRING},
            {name: 'birthDate', type: Ext.data.Types.DATE, dateFormat: 'd/m/Y'},
            {name: 'role', type: Ext.data.Types.STRING},
            {name: 'roleId', type: Ext.data.Types.NUMBER},
            {name: 'groupList', type: Ext.data.Types.STRING}
        ],
        sortInfo: {
            field: 'secondName',
            dir : 'ASC'
        },
        baseParams: {
            start: 0,          
            limit: 15
        },
        listeners : {
            exception : exceptionHandler
	}
    });
    
    store.load();
    
    var cols = [{
                    xtype : 'actioncolumn',
                    width : 60,
                    hideable : false,
                    items : [ {
                                icon : 'public/images/pencil.png',
                                tooltip : 'Modifica',
                                handler : function(grid, rowIndex, colIndex) {
                                        var rec = grid.getStore().getAt(rowIndex);
                                        modifyUser(rec.data);
                                }
                            }, {
                                icon : 'public/images/error.gif',
                                tooltip : 'Sterge',
                                handler : function(grid, rowIndex, colIndex) {
                                        var rec = grid.getStore().getAt(rowIndex);
                                        deleteUser(rec.data.id);
                                }
                            } ]
                },{
                    dataIndex: 'secondName',
                    header: 'Nume', 
                    sortable: true,
                    hideable: false
                },{
                    dataIndex: 'firstName',
                    header: 'Prenume', 
                    sortable: true,
                    hideable: true
                },{
                    dataIndex: 'login',
                    header: 'Login', 
                    sortable: true,
                    hideable: false
                },{
                    dataIndex: 'email',
                    header: 'Email', 
                    sortable: true,
                    hideable: true
                },{
                    dataIndex: 'phone',
                    header: 'Telefon', 
                    sortable: true,
                    hideable: true
                },{
                    dataIndex: 'birthDate',
                    header: 'Data nasterii', 
                    sortable: true,
                    hideable: true,
                    renderer: Ext.util.Format.dateRenderer('d M, Y')
                },{
                    dataIndex: 'role',
                    header: 'Rol', 
                    sortable: true,
                    hideable: true
                } 
    ];

    return new Ext.grid.GridPanel({
        id: 'userGrid',
        title: 'Utilizatori',
        height: 300,
        width: 1000,
        store: store,
        columns : cols,
        viewConfig: {
            forceFit: true
        },
        loadMask: true,
        autoScroll: true,
        bbar: new Ext.PagingToolbar({
            pageSize: 15,
            store: store,
            displayInfo: true,
            displayMsg: 'Afisarea utilizatorilor {0} - {1} din {2}',
            emptyMsg: "Nu sunt date de afisat",
            items : [
                {
                    xtype : 'button',
                    icon : 'public/images/button_add.gif',
                    text : 'Adauga utilizator',
                    handler : openUserPage,
                    style : 'margin:10px'
                }
            ]
        })
    });
};

function createUserForm() {
    var roleProxy = new Ext.data.HttpProxy({
        url: Routing.generate('role_list')
    });

    var roleStore = new Ext.data.JsonStore({
        id: 'roleStore',
        proxy: roleProxy,
        idProperty: 'id',
        root: 'data',
        exceptionHandler : exceptionHandler,
        fields: [
            {name: 'id', type: Ext.data.Types.NUMBER},
            {name: 'label', type: Ext.data.Types.STRING}
        ],
        listeners : {
            exception : exceptionHandler
	}
    });
    
    roleStore.load();

    return new Ext.form.FormPanel({
        title: 'Adauga/Modifica utilizator',
        bodyStyle : 'padding:5px 5px 0',
        labelWidth: 75,
        defaults : {
            xtype : 'textfield',
            anchor: '95%'
        },
        autoScroll : true,
        items : [
            {
                xtype: 'hidden',
                id: 'idField'
            },
            {
                id: 'secondNameField',
                fieldLabel: 'Nume',
                labelSeparator : ' *:',
                vtype: 'notBlank'
            },{
                id: 'firstNameField',
                fieldLabel: 'Prenume',               
                labelSeparator : ' :'
            },{
                id: 'loginField',
                fieldLabel: 'Login',               
                labelSeparator : ' *:',
                vtype: 'notBlank'
            },{
                id: 'passwordField',
                fieldLabel: 'Parola',               
                labelSeparator : ' *:',
                vtype: 'notBlank'
            },{
                xtype : 'combo',
                fieldLabel : 'Rol',
                labelSeparator : ' *:',
                id : 'roleField',
                valueField : 'id',
                displayField : 'label',
                store : roleStore,
                triggerAction : 'all',
                editable : false,
                mode : 'local'
            },{
                xtype : 'panel',
                id : 'groupField',
                border: false,
                autoHeight: true,
                fieldLabel : 'Grupuri',
                labelSeparator : ' *:',
                html : '<div style="float: left;">' +
                             ' <select id="groupList" size="5" style="width: 210px;"></select>' +
                        '</div>' +
                        '<div style="margin-top: 20px; float: right;"> ' +
                            '<a href="#" onclick="showGroupWindow();"> <img src="public/images/button_add.gif" title="Adauga grup"/> </a>' + 
                            '</br> </br>' +
                            '<a href="#" onclick="removeGroup()"> <img src="public/images/button_delete.gif" title="Sterge grup"/> </a>' + 
                        '</div>'
            },{
                id: 'emailField',
                fieldLabel: 'Email',               
                labelSeparator : ' *:',
                vtype : 'emailValid'
            },{
                id: 'phoneField',
                fieldLabel: 'Telefon',               
                labelSeparator : ' :',
                vtype: 'telNumber'
            },{
                id: 'birthDateField',
                xtype: 'datefield',
                fieldLabel: 'Data nasterii',               
                labelSeparator : ' :',
                format : 'd/m/Y'
            }
        ],
        buttons: [{
            icon : 'public/images/button_save.png',
            text : 'Salveaza',
            handler : saveUser,
            style : 'margin:10px'
        },{
            icon : 'public/images/button_reset.png',
            text : 'Anuleaza',
            handler : closeUserPage,
            style : 'margin:10px'
        }]
    });
}

function createGroupGrid(){
    var proxy = new Ext.data.HttpProxy({
        url: Routing.generate('group_list')
    });

    var store = new Ext.data.JsonStore({
        id: 'groupStore',
        proxy: proxy,
        idProperty: 'id',
        root: 'data',
        exceptionHandler : exceptionHandler,
        fields: [
            {name: 'id', type: Ext.data.Types.NUMBER},
            {name: 'name', type: Ext.data.Types.STRING},
            {name: 'description', type: Ext.data.Types.STRING},
            {name: 'year', type: Ext.data.Types.NUMBER}
        ],
        listeners : {
            load: function(store) {
                var recordsToSelect = [];
                Ext.each(groupIds, function(groupId){
                    var index = store.findExact('id', groupId);
                    var record = store.getAt(index);
                    recordsToSelect.push(record);                    
                 });
                 Ext.getCmp('groupGrid').getSelectionModel().selectRecords(recordsToSelect, true);
            },
            exception: exceptionHandler
        }
    });
    
    var groupSelModel = new Ext.grid.CheckboxSelectionModel({
        checkOnly: false,
        singleSelect: false,
        sortable: false
    });
    
    var cols = [{
                    dataIndex: 'name',
                    header: 'Nume', 
                    sortable: true,
                    hideable: false
                },{
                    dataIndex: 'description',
                    header: 'Descriere', 
                    sortable: true,
                    hideable: false
                },{
                    dataIndex: 'year',
                    header: 'Anul crearii', 
                    sortable: true,
                    hideable: false
                },
                groupSelModel
    ];

    return new Ext.grid.GridPanel({
        id: 'groupGrid',
        height: 300,
        width: 600,
        store: store,
        columns : cols,
        sm: groupSelModel,
        viewConfig: {
            forceFit: true
        },
        loadMask: true,
        autoScroll: true
    });
};

function showGroupWindow() {
    if (winChooseGroups == null) {
        var groupGrid = createGroupGrid();
        winChooseGroups = new Ext.Window({
                title : 'Grupuri',
                height : 450,
                width : 800,
                modal : true,
                layout : 'fit',
                closeAction: 'hide',
                items : [ groupGrid ],
                buttons : [ {
                        text : 'Selecteaza',
                        handler : addGroups
                } ]
        });
    }
    Ext.getCmp('groupGrid').store.load();
    winChooseGroups.show();
    
}

function createUserPanel(){
    var userGrid = createUserGrid();
    var userForm = createUserForm();
    
    new Ext.Panel({
            width : 1024,
            height : 450,
            layout : 'border',
            items : [ 
                {
                    region : 'west',
                    xtype : 'panel',
                    collapsible : true,
                    collapsed : true,
                    width : 350,
                    items : [ userForm ],
                    id : 'west-region-container',
                    layout : 'fit'
                }, {
                    region : 'center',
                    items : [ userGrid ],
                    width : 900,
                    layout : 'fit'
                } ],
            renderTo: 'user-grid'
    });
}

function openUserPage() {
    if (Ext.getCmp('west-region-container').collapsed == true) {
        Ext.getCmp('west-region-container').toggleCollapse();
    }

    resetFields();
}

function closeUserPage() {
    Ext.getCmp('west-region-container').toggleCollapse();

    resetFields();
}

function modifyUser(data) {
    if (Ext.getCmp('west-region-container').collapsed == true) {
        Ext.getCmp('west-region-container').toggleCollapse();
    }
    
    Ext.getCmp('idField').setValue(data.id);
    Ext.getCmp('firstNameField').setValue(data.firstName);
    Ext.getCmp('secondNameField').setValue(data.secondName);
    Ext.getCmp('loginField').setValue(data.login);
    Ext.getCmp('passwordField').setValue(data.password);
    Ext.getCmp('roleField').setValue(data.roleId);
    Ext.getCmp('emailField').setValue(data.email);
    Ext.getCmp('phoneField').setValue(data.phone);
    Ext.getCmp('birthDateField').setValue(data.birthDate);
    groupIds = [];
    document.getElementById('groupList').innerHTML = '';
    var groupList = Ext.decode(data.groupList);
    Ext.each(groupList, function(group) {
        var option = document.createElement("option");
        option.text = group.name;
        option.value = group.id;        
        document.getElementById('groupList').add(option);
        groupIds.push(parseInt(group.id));
    });
}

function addGroups() {
    var selections = Ext.getCmp('groupGrid').getSelectionModel().getSelections();
    groupIds = [];
    document.getElementById('groupList').innerHTML = '';
    Ext.each(selections, function(selection) {
        groupIds.push(selection.data.id);
        var option = document.createElement("option");
        option.text = selection.data.name;
        option.value = selection.data.id;
        document.getElementById('groupList').add(option);
    });
    winChooseGroups.hide();
}

function removeGroup() {
    var el = document.getElementById('groupList');
    if (el.selectedIndex > -1) {
        var groupId = el.options[el.selectedIndex].value;
        groupIds.remove(parseInt(groupId));
        el.remove(el.selectedIndex);
    }
}

function saveUser() {
    var validationMessage = '';
    
    if (isBlank(Ext.getCmp('secondNameField').getValue())) {
        validationMessage += 'Campul "Nume" este gol!' + '</br>';
    }
    if (isBlank(Ext.getCmp('loginField').getValue())) {
        validationMessage += 'Campul "Login" este gol!' + '</br>';
    }
    if (isBlank(Ext.getCmp('passwordField').getValue())) {
        validationMessage += 'Campul "Parola" este gol!' + '</br>';
    }
    if (isBlank(Ext.getCmp('roleField').getValue())) {
        validationMessage += 'Alegeti rolul!' + '</br>';
    }
    if (document.getElementById('groupList').innerHTML == '') {
        validationMessage += 'Alegeti cel putin un grup!' + '</br>';
    }
    if (isBlank(Ext.getCmp('emailField').getValue())) {
        validationMessage += 'Campul "Email" este gol!' + '</br>';
    } else if (!Ext.getCmp('emailField').isValid()) {
        validationMessage += 'Email invalid!' + '</br>';
    }
    if (!Ext.getCmp('phoneField').isValid()) {
        validationMessage += 'Telefonul nu respecta sablonul!' + '</br>';
    }
    if (!Ext.getCmp('birthDateField').isValid()) {
        validationMessage += 'Data nasterii este invalida!' + '</br>';
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
            url: Routing.generate('user_save'),
            success: saveUserCallback,
            params: { 
                id: Ext.getCmp('idField').getValue(),
                firstName: Ext.getCmp('firstNameField').getValue(),
                secondName: Ext.getCmp('secondNameField').getValue(),
                login: Ext.getCmp('loginField').getValue(),
                password: Ext.getCmp('passwordField').getValue(),
                roleId: Ext.getCmp('roleField').getValue(),
                groupIds: groupIds.join(),
                email: Ext.getCmp('emailField').getValue(),
                phone: Ext.getCmp('phoneField').getValue(),
                birthDate: Ext.util.Format.date(Ext.getCmp('birthDateField').getValue(),'d/m/Y')
            }
        });
    }
}

function saveUserCallback(resultJSON) {
    if (!errorResultObj(resultJSON)) {
        Ext.getCmp('userGrid').store.load();
        if (Ext.getCmp('west-region-container').collapsed == false) {
            Ext.getCmp('west-region-container').toggleCollapse();
        }
        resetFields();
    }
}

function deleteUser(userId) {
    Ext.Msg.show({
        title : 'Confirmare stergere',
        msg : 'Sunte-ti sigur ca stergeti acest utilizator?',
        buttons : Ext.Msg.YESNO,
        icon : Ext.MessageBox.QUESTION,
        fn : function(btn) {
            if (btn == 'yes') {
                Ext.Ajax.request({
                    url: Routing.generate('user_delete'),
                    success: deleteUserCallback,
                    params: { 
                        id: userId
                    }
                });
            }
        }
    });
    
}

function deleteUserCallback(resultJSON) {
    if (!errorResultObj(resultJSON)) {
        Ext.getCmp('userGrid').store.load();
        if (Ext.getCmp('west-region-container').collapsed == false) {
            Ext.getCmp('west-region-container').toggleCollapse();
        }
        resetFields();
    }
}

function resetFields() {
    Ext.getCmp('idField').setValue(0);
    Ext.getCmp('firstNameField').setValue('');
    Ext.getCmp('secondNameField').setValue('');
    Ext.getCmp('loginField').setValue('');
    Ext.getCmp('passwordField').setValue('');
    Ext.getCmp('roleField').setValue('');
    Ext.getCmp('emailField').setValue('');
    Ext.getCmp('phoneField').setValue('');
    Ext.getCmp('birthDateField').setValue('');
    groupIds = [];
    document.getElementById('groupList').innerHTML = '';
}

function exceptionHandler(ex) {
   Ext.Msg.show({
           title : 'Eroare',
           msg : 'S-a produs o eroare tehnica',
           buttons : Ext.Msg.OK,
           icon : Ext.MessageBox.ERROR
   });
}
        
Ext.onReady(function(){
    Ext.QuickTips.init();
    
    Ext.form.Field.prototype.msgTarget = 'side';
    
    createUserPanel();
});