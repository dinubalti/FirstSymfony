var galleryWindow = null;
var addImageWindow = null;

function createUserImagesPanel() {
    var proxy = new Ext.data.HttpProxy({
        url: Routing.generate('user_img_list')
    });

    store = new Ext.data.JsonStore({
        id: 'imgStore',
        proxy: proxy,
        idProperty: 'id',
        root: 'data',
        exceptionHandler : exceptionHandler,
        baseParams : {
            userId: document.getElementById('userId').value,
            groupId : 0
        },
        fields: [
            {name: 'id', type: Ext.data.Types.NUMBER},
            {name: 'name', type: Ext.data.Types.STRING},
            {name: 'description', type: Ext.data.Types.STRING},
            {name: 'imageId', type: Ext.data.Types.NUMBER},
            {name: 'extension', type: Ext.data.Types.STRING},
            {name: 'group', type: Ext.data.Types.STRING}
        ],
        listeners : {
            exception: exceptionHandler
        }
    });
    
    store.load();
    
    var dataview = new Ext.DataView({
        store: store,
        tpl  : new Ext.XTemplate(
            '<ul>',
                '<tpl for=".">',
                   '<li class="photo">',
                        '<img width="150" height="150" src="../public/photos/{imageId}.{extension}" onclick="showImageGalleryWindow({imageId},\'{extension}\',\'{[this.escapeHtml(values.name)]}\',\'{[this.escapeHtml(values.description)]}\',\'{[this.escapeHtml(values.group)]}\');"/>',
                        '<strong ext:qtip="{[this.escapeHtml(this.escapeHtml(values.name))]}">{[this.escapeHtml(values.name)]}</strong>',
                        '<i ext:qtip="{[this.escapeHtml(this.escapeHtml(values.group))]}">({[this.escapeHtml(values.group)]})</i>',
                        '<span ext:qtip="{[this.escapeHtml(this.escapeHtml(values.description))]}">{[this.escapeHtml(values.description)]}</span>',
                    '</li>',
                '</tpl>',
            '</ul>',
            {        
                escapeHtml: function(value){
                   return escapeHtml(value);
                }
            }
        ),       
        id: 'photos',
        itemSelector: 'li.photo',
        singleSelect: true,
        multiSelect : true,
        autoScroll  : true
    });

    var groupProxy = new Ext.data.HttpProxy({
        url: Routing.generate('user_img_group_list')
    });
    
    var groupStore = new Ext.data.JsonStore({
        id: 'groupStore',
        proxy: groupProxy,
        idProperty: 'id',
        root: 'data',
        baseParams: {
            userId: document.getElementById('userId').value
        },
        exceptionHandler : exceptionHandler,
        fields: [
            {name: 'id', type: Ext.data.Types.NUMBER},
            {name: 'label', type: Ext.data.Types.STRING}
        ],
        listeners : {
            load : function(store) {
               var emptyRecord = new store.recordType({ id: 0, label: '' });
               store.insert(0, emptyRecord); 
            },
            exception : exceptionHandler
	}
    });
    groupStore.load();

    var tbar = new Ext.Toolbar({
        items  : [
            'Sorteaza dupa:',
            createSorterButton({
                text: 'Nume',
                sortData: {
                    field: 'name',
                    direction: 'DESC'
                }
            }),
            createSorterButton({
                text: 'Descriere',
                sortData: {
                    field: 'description',
                    direction: 'DESC'
                }
            }),
            createSorterButton({
                text: 'Grup',
                sortData: {
                    field: 'group',
                    direction: 'DESC'
                }
            }),
            '&nbsp;&nbsp;&nbsp;',
            'Filtreaza dupa:',
            '&nbsp;&nbsp;',
            'Grup:',
            {
                xtype : 'combo',
                id : 'groupCombo',
                valueField : 'id',
                displayField : 'label',
                store : groupStore,
                triggerAction : 'all',
                editable : false,
                mode : 'local',
                tpl : '<tpl for="."><div class="x-combo-list-item">&nbsp;{label:htmlEncode}</div></tpl>',
                listeners : {
                    select : function( combo, record, index ) {
                        store.load({
                            params: {
                                groupId: combo.getValue()
                            }
                        });
                    }
                }
            }
        ]
    });

    new Ext.Panel({
        id: 'imagesPanel',
        title: 'Fotografiile personale',
        layout: 'fit',
        items : dataview,
        height: 550,
        width : 1050,
        tbar  : tbar,
        renderTo: 'user-images',
        bbar: [
            '->',
            {
                xtype: 'button',
                text: 'Adauga fotografii',
                icon: '../public/images/button_add.gif',
                handler: showAddImageWindow
            },
            {
                xtype: 'button',
                text: 'Sterge fotografii',
                icon: '../public/images/button_delete.gif',
                handler: deleteSelectedImages
            }
        ]
    });

    //perform an initial sort
    doSort([{
        field: 'name',
        direction: 'ASC'
    }]);  
}

/**
 * Tells the store to sort itself according to our sort data
 */
function doSort(sorters) {
     Ext.StoreMgr.lookup("imgStore").sort(sorters, "ASC");
}

/**
 * Callback handler used when a sorter button is clicked or reordered
 * @param {Ext.Button} button The button that was clicked
 * @param {Boolean} changeDirection True to change direction (default). Set to false for reorder
 * operations as we wish to preserve ordering there
 */
function changeSortDirection(button, changeDirection) {
    var sortData = button.sortData,
        iconCls  = button.iconCls;

    if (sortData != undefined) {
        if (changeDirection !== false) {
            button.sortData.direction = button.sortData.direction.toggle("ASC", "DESC");
            button.setIconClass(iconCls.toggle("sort-asc", "sort-desc"));
        }

        Ext.StoreMgr.lookup("imgStore").clearFilter();
        var sorters = [];
        sorters.push(button.sortData);       
        doSort(sorters);
    }
}

/**
 * Convenience function for creating Toolbar Buttons that are tied to sorters
 * @param {Object} config Optional config object
 * @return {Ext.Button} The new Button object
 */
function createSorterButton(config) {
    config = config || {};

    Ext.applyIf(config, {
        listeners: {
            click: function(button, e) {
                changeSortDirection(button, true);                    
            }
        },
        iconCls: 'sort-' + config.sortData.direction.toLowerCase(),
        reorderable: true
    });

    return new Ext.Button(config);
}

function showImageGalleryWindow(imageId, extension, name, description, group) {
    if(galleryWindow == null){            
            galleryWindow = new Ext.Window({
                layout:'fit',
                width:800,
                height:600,
                closeAction:'hide',
                modal: true,
                autoScroll: true,
                bodyStyle:{'background-color': 'white'},
                items: [
                    {
                        xtype: 'panel',
                        id: 'imageGalleryPanel',
                        style: 'text-align: center; margin-top: 20px; margin-left: 20px; margin-right: 20px;',
                        border: false,
                        autoWidth: true,
                        autoHeight: true
                    }
                ],
                bbar: [{
                    id: 'leftArrow'
                },
                '->',
                {
                    id: 'nextArrow'
                }]
            });
        }
        
        galleryWindow.show();
        updateImageGallery(imageId, extension, name, description, group);

}

function showPreviousImage(imageId) {
    var store = Ext.StoreMgr.lookup("imgStore");
    var currentIndex = store.findExact('imageId', imageId);
    var prevRecord = store.getAt(currentIndex - 1);  
    updateImageGallery(prevRecord.data.imageId, prevRecord.data.extension, prevRecord.data.name, prevRecord.data.description, prevRecord.data.group);
}

function showNextImage(imageId) {
    var store = Ext.StoreMgr.lookup("imgStore");
    var currentIndex = store.findExact('imageId', imageId);
    var nextRecord = store.getAt(currentIndex + 1);
    updateImageGallery(nextRecord.data.imageId, nextRecord.data.extension, nextRecord.data.name, nextRecord.data.description, nextRecord.data.group);
}

function updateImageGallery(imageId, extension, name, description, group) {
    var store = Ext.StoreMgr.lookup("imgStore");
    var recordIndex = store.findExact('imageId', imageId);
    Ext.getCmp('imageGalleryPanel').update( '<img src="../public/photos/' + imageId + '.' +extension+ '" height="450px"/> </br>' +
                                '<b>' + escapeHtml(name) + '</b> </br>' +
                                '<i>(' + escapeHtml(group) + ')</i> </br>' +
                                '<span>' + escapeHtml(description) + '</span>');
    if (recordIndex !== 0) {        
        Ext.getCmp('leftArrow').update('<img src="../public/images/arrow_left.png" onclick="showPreviousImage(' + imageId + ');"/>');
    } else {
        Ext.getCmp('leftArrow').update('');
    }
    
    if (recordIndex !== store.data.items.length - 1) {
        Ext.getCmp('nextArrow').update('<img src="../public/images/arrow_right.png" onclick="showNextImage(' + imageId + ');"/>');
    } else {
        Ext.getCmp('nextArrow').update('');
    }
}

function showAddImageWindow() {
    if(addImageWindow == null){  
        var msg = function(title, msg){
            Ext.Msg.show({
                title: title,
                msg: msg,
                minWidth: 200,
                modal: true,
                icon: Ext.Msg.INFO,
                buttons: Ext.Msg.OK
            });
        };
        var uploadGroupProxy = new Ext.data.HttpProxy({
            url: Routing.generate('user_img_group_list')
        });

        var uploadGroupStore = new Ext.data.JsonStore({
            id: 'uploadGroupStore',
            proxy: uploadGroupProxy,
            idProperty: 'id',
            root: 'data',
            baseParams: {
                userId: document.getElementById('userId').value
            },
            exceptionHandler : exceptionHandler,
            fields: [
                {name: 'id', type: Ext.data.Types.NUMBER},
                {name: 'label', type: Ext.data.Types.STRING}
            ]
        });
        uploadGroupStore.load();
        
        var fp = new Ext.FormPanel({
            fileUpload: true,
            autoWidth: true,
            frame: true,
            autoHeight: true,
            bodyStyle: 'padding: 10px 10px 0 10px;',
            labelWidth: 80,
            defaults: {
                anchor: '95%'
            },
            items: [{
                xtype: 'hidden',
                id: 'photoUserId',
                value: document.getElementById('userId').value
            },{
                xtype: 'textfield',
                fieldLabel: 'Nume',
                labelSeparator : ' *:',
                allowBlank: false,
                id: 'photoName'
            },{
                xtype: 'textarea',
                fieldLabel: 'Descriere',
                labelSeparator : ' :',
                name: 'photoDescription'
            },{
                xtype : 'combo',
                id : 'uploadGroupCombo',
                valueField : 'id',
                displayField : 'label',
                fieldLabel: 'Grup',
                labelSeparator : ' *:',
                store : uploadGroupStore,
                triggerAction : 'all',
                editable : false,
                mode : 'local',
                allowBlank: false,
                id: 'photoGroup',
                listeners: {
                    select: function(combo, record, index) {
                        Ext.getCmp('photoGroupId').setValue(combo.getValue());
                    }
                }
            },{
                xtype: 'hidden',
                id: 'photoGroupId'
            },{
                xtype: 'fileuploadfield',
                id: 'form-file',
                emptyText: 'Selecteaza o imagine',
                fieldLabel: 'Fotografie',
                labelSeparator : ' *:',
                name: 'photo-path',
                accept: ['jpg', 'jpeg', 'png', 'bmp', 'gif'],
                allowBlank: false,
                buttonText: '',
                buttonCfg: {
                    iconCls: 'upload-icon'
                },
                listeners     : {
                    fileselected : function() {
                        var indexofPeriod = this.getValue().lastIndexOf('.'),
                        uploadedExtension = this.getValue().substr(indexofPeriod + 1, this.getValue().length - indexofPeriod);

                        if (this.accept.indexOf(uploadedExtension.toLowerCase()) === -1){
                            Ext.MessageBox.show({
                                title   : 'Eroare tip fisier',
                                msg   : 'Incarcati fisiere numai de tipul :  ' + this.accept.join() + '!',
                                buttons : Ext.Msg.OK,
                                icon  : Ext.Msg.ERROR
                            });
                            Ext.getCmp('form-file').setRawValue('Selecteaza o imagine');
                        }
                    }
                }
            }],
            buttons: [{
                text: 'Salveaza',
                icon : '../public/images/button_save.png',
                handler: function(){
                    if(fp.getForm().isValid()){
                            fp.getForm().submit({
                                url: Routing.generate('user_img_upload_image'),
                                waitMsg: 'Uploading your photo...',
                                sucess: function(fp, o){
                                    msg('Success', 'Processed file "'+o.result.file+'" on the server');
                                }
                            });
                    }
                }
            },{
                text: 'Reseteaza',
                icon : '../public/images/button_reset.png',
                handler: function(){
                    fp.getForm().reset();
                }
            }]
        });
        addImageWindow = new Ext.Window({
            layout:'fit',
            width:600,
            autoHeight: true,
            closeAction:'hide',
            modal: true,
            autoScroll: true,
            title: 'Incarca fotografii',
            bodyStyle:{'background-color': 'white'},
            items: [
                fp
            ],
            listeners: {
                hide: function() {
                    fp.getForm().reset();
                    Ext.StoreMgr.lookup("imgStore").load();
                }
            }
        });
    }
        
    addImageWindow.show();
}

function deleteSelectedImages() {
    var store = Ext.StoreMgr.lookup("imgStore");
    var imagesToDelete = [];
    Ext.each(Ext.getCmp('imagesPanel').items.items[0].selected.elements, function(selectedElement) {
        var record = store.getAt(selectedElement.viewIndex);
        imagesToDelete.push(record.data.id);
    });
    if (imagesToDelete.length === 0) {
        Ext.Msg.show({
            title : 'Selectati imaginile',
            msg : 'Selectati imaginile pe care doriti sa le stergeti!',
            buttons : Ext.Msg.OK,
            icon : Ext.MessageBox.INFO
        });
    } else {
        Ext.Msg.show({
            title : 'Confirmare stergere',
            msg : 'Sunteti sigur ca stergeti imaginile selectate?',
            buttons : Ext.Msg.YESNO,
            icon : Ext.MessageBox.QUESTION,
            fn : function(btn) {
                if (btn == 'yes') {
                    Ext.Ajax.request({
                        url: Routing.generate('user_img_delete_images'),
                        success: deleteSelectedImagesCallback,
                        params: { 
                            imageIds: imagesToDelete.join()
                        }
                    });
                }
            }
        });
    }
}

function deleteSelectedImagesCallback(resultJSON) {
    if (!errorResultObj(resultJSON)) {
        Ext.StoreMgr.lookup("imgStore").load();
    }
}

Ext.onReady(function() {
    Ext.QuickTips.init();
    
    createUserImagesPanel();    
});