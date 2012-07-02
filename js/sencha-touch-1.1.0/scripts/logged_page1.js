Ext.setup({
    icon: 'icon.png',
    tabletStartupScreen: 'tablet_startup.png',
    phoneStartupScreen: 'phone_startup.png',
    glossOnIcon: false,
    onReady: function() {
        
        var form;
        Ext.regModel('Country', {
            fields: ['id', 'name']
        }); 
        
        //Ext.getBody().mask('Loading...', 'x-mask-loading', false);  
        //Ext.getBody().unmask();     
        
        var countryStore = new Ext.data.Store({
            autoLoad: true,
            model: "Country",
            proxy: {
                type: 'ajax',
                url : 'ajax/mobile/doRequest.php?type=getCountries',
                reader: {
                    type: 'json',
                    root: 'data'
                }
            }
        });              
        Ext.regModel('Industry', {
            fields: ['id', 'industry', 'sector']
        });        
        var industryStore = new Ext.data.Store({
            autoLoad: true,
            model: "Industry",
            proxy: {
                type: 'ajax',
                url : 'ajax/mobile/doRequest.php?type=getIndustry',
                reader: {
                    type: 'json',
                    root: 'data'
                }
            }
        });              
        
        var formBase = {
            scroll: 'vertical',
            url   : 'ajax/mobile/doRequest.php?type=userLogin',
            standardSubmit : false,
            items: [
                    {
                        xtype: 'panel',
                        html: '<img src="images/deal_data_logo.png" style="margin: 0 auto;"/> <br /> Use 2 Weeks now',
                        titleCollapse: true,
                        autoWidth: true,
                        style: 'text-align:center',
                    }, {
                        xtype: 'fieldset',
                        items: [{
                            xtype: 'selectfield',
                            name: 'country',
                            //store: countryStore,
                            displayField: 'name',
                            valueField: 'name',
                            emptyText: 'Choose a Country',
                            options: [
                                {'id': 0, 'name' : 'Choose a Country' }
                            ]
                        }]                         
                    }, {
                        xtype: 'fieldset',
                        items: [{
                            xtype: 'selectfield',
                            name: 'industry',
                            store: industryStore,
                            displayField: 'industry',
                            valueField: 'industry',
                            emptyText: 'Choose Industry'                         
                        }]                         
                    }, {
                        xtype: 'panel',
                        html: 'Copyright &copy; 2011 deal-data.com <br /> <a href="#"> Privacy policy </a> | <a href="#"> Legal Notices </a>',
                        titleCollapse: true,
                        autoWidth: true,
                        style: 'text-align:center; font-size: 12px;'
                    },                
            ],
            listeners : {
                submit : function(form, result){
                    window.location = window.location;
                },
                exception : function(form, result){
                    //console.log('failure', Ext.toArray(arguments));
                    //console.log(result.errors);
                    Ext.Msg.alert('Error', result.error);
                    var overlay = new Ext.Panel({
                        floating: true,
                        modal: true,
                        centered: true,
                        width: Ext.is.Phone ? 200 : 400,
                        styleHtmlContent: true,
                        scroll: 'vertical',
                        html: result.error,
                        style: 'font-size: 16px;'
                    });
                    //overlay.show()                    
                }
            },
            dockedItems: [{
                xtype: 'toolbar',
                ui: 'light',
                items: [{
                    text: 'Logout',
                    iconAlign: 'bottom',
                    iconMask: false
                }],
                dock: 'bottom',
                layout: {
                    pack: 'right'
                }                
            }]
        };
        
        if (Ext.is.Phone) {
            formBase.fullscreen = true;
        } else {
            Ext.apply(formBase, {
                autoRender: true,
                floating: true,
                modal: true,
                centered: true,
                hideOnMaskTap: false,
                height: 385,
                width: 480
            });
        }
        
        form = new Ext.form.FormPanel(formBase);
        form.show();
    }
});