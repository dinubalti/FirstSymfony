function createGroupGrid(){
    var proxy = new Ext.data.HttpProxy({
        url: Routing.generate('group_list')
    });

    var store = new Ext.data.JsonStore({
        id: 'groupStore',
        proxy: proxy,
        idProperty: 'id',
        root: 'data',
        totalProperty: 'totalCount',
        remoteSort: true,
        exceptionHandler : exceptionHandler,
        fields: [
            {name: 'id', type: Ext.data.Types.NUMBER},
            {name: 'name', type: Ext.data.Types.STRING},
            {name: 'description', type: Ext.data.Types.STRING},
            {name: 'year', type: Ext.data.Types.NUMBER}
        ],
        sortInfo: {
            field: 'name',
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
                    width : 30,
                    hideable : false,
                    align: 'center',
                    items : [ {
                                icon : '../public/images/pencil.png',
                                tooltip : 'Modifica',
                                handler : function(grid, rowIndex, colIndex) {
                                        var rec = grid.getStore().getAt(rowIndex);
                                        modifyGroup(rec.data);
                                }
                            }, {
                                icon : '../public/images/error.gif',
                                tooltip : 'Sterge',
                                handler : function(grid, rowIndex, colIndex) {
                                        var rec = grid.getStore().getAt(rowIndex);
                                        deleteGroup(rec.data.id);
                                }
                            } ]
                },{
                    dataIndex: 'name',
                    header: 'Nume', 
                    sortable: true,
                    hideable: false,
                    renderer : escapeHtml,
                    width : 60
                },{
                    dataIndex: 'description',
                    header: 'Descriere', 
                    sortable: true,
                    hideable: true,
                    renderer : escapeHtml
                },{
                    dataIndex: 'year',
                    header: 'Anul crearii', 
                    sortable: true,
                    hideable: true,
                    renderer : escapeHtml,
                    width : 30                   
                } 
    ];

    return new Ext.grid.GridPanel({
        id: 'groupGrid',
        title: 'Grupuri',
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
            displayMsg: 'Afisarea grupurilor {0} - {1} din {2}',
            emptyMsg: "Nu sunt date de afisat",
            items : [
                {
                    xtype : 'button',
                    icon : '../public/images/button_add.gif',
                    text : 'Adauga grup',
                    handler : openGroupPage,
                    style : 'margin:10px'
                }
            ]
        })
    });
};

function createGroupForm() {
    return new Ext.form.FormPanel({
        title: 'Adauga/Modifica grup',
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
                id: 'nameField',
                fieldLabel: 'Nume',
                labelSeparator : ' *:',
                vtype: 'notBlank'
            },{
                id: 'descriptionField',
                fieldLabel: 'Descriere',               
                labelSeparator : ' :'
            },{
                xtype: 'numberfield',
                id: 'yearField',
                fieldLabel: 'Anul crearii',               
                labelSeparator : ' :',
                allowDecimals : false,
                allowNegative : false,
                autoCreate : {
                    tag : 'input', 
                    maxlength : 4
                }
            }
        ],
        buttons: [{
            icon : '../public/images/button_save.png',
            text : 'Salveaza',
            handler : saveGroup,
            style : 'margin:10px'
        },{
            icon : '../public/images/button_reset.png',
            text : 'Anuleaza',
            handler : closeGroupPage,
            style : 'margin:10px'
        }]
    });
}

function createGroupPanel(){
    var groupGrid = createGroupGrid();
    var groupForm = createGroupForm();
    
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
                    items : [ groupForm ],
                    id : 'west-region-container',
                    layout : 'fit'
                }, {
                    region : 'center',
                    items : [ groupGrid ],
                    width : 900,
                    layout : 'fit'
                } ],
            renderTo: 'group-grid'
    });
}

function openGroupPage() {
    if (Ext.getCmp('west-region-container').collapsed == true) {
        Ext.getCmp('west-region-container').toggleCollapse();
    }

    resetFields();
}

function closeGroupPage() {
    Ext.getCmp('west-region-container').toggleCollapse();

    resetFields();
}

function modifyGroup(data) {
    if (Ext.getCmp('west-region-container').collapsed == true) {
        Ext.getCmp('west-region-container').toggleCollapse();
    }
    
    Ext.getCmp('idField').setValue(data.id);
    Ext.getCmp('nameField').setValue(data.name);
    Ext.getCmp('descriptionField').setValue(data.description);
    Ext.getCmp('yearField').setValue(data.year);    
}

function saveGroup() {
    var validationMessage = '';
    
    if (isBlank(Ext.getCmp('nameField').getValue())) {
        validationMessage += 'Campul "Nume" este gol!';
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
            url: Routing.generate('group_save'),
            success: saveGroupCallback,
            params: { 
                id: Ext.getCmp('idField').getValue(),
                name: Ext.getCmp('nameField').getValue(),
                description: Ext.getCmp('descriptionField').getValue(),
                year: Ext.getCmp('yearField').getValue(),
            }
        });
    }
}

function saveGroupCallback(resultJSON) {
    if (!errorResultObj(resultJSON)) {
        Ext.getCmp('groupGrid').store.load();
        if (Ext.getCmp('west-region-container').collapsed == false) {
            Ext.getCmp('west-region-container').toggleCollapse();
        }
        resetFields();
    }
}

function deleteGroup(groupId) {
    Ext.Msg.show({
        title : 'Confirmare stergere',
        msg : 'Sunte-ti sigur ca stergeti acest grup?',
        buttons : Ext.Msg.YESNO,
        icon : Ext.MessageBox.QUESTION,
        fn : function(btn) {
            if (btn == 'yes') {
                Ext.Ajax.request({
                    url: Routing.generate('group_delete'),
                    success: deleteGroupCallback,
                    params: { 
                        id: groupId
                    }
                });
            }
        }
    });
    
}

function deleteGroupCallback(resultJSON) {
    if (!errorResultObj(resultJSON)) {
        Ext.getCmp('groupGrid').store.load();
        if (Ext.getCmp('west-region-container').collapsed == false) {
            Ext.getCmp('west-region-container').toggleCollapse();
        }
        resetFields();
    }
}

function resetFields() {
    Ext.getCmp('idField').setValue(0);
    Ext.getCmp('nameField').setValue('');
    Ext.getCmp('descriptionField').setValue('');
    Ext.getCmp('yearField').setValue('');
}
        
Ext.onReady(function(){
    Ext.QuickTips.init();
    
    createGroupPanel();
});