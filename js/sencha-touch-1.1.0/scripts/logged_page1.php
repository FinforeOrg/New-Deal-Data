<?php
header( 'Content-Type: application/javascript' );
require_once(dirname(dirname(dirname(dirname(__FILE__)))) . '/classes/mobileApp.php');
$mobileApplication = new MobileApp();  
$latestResults = $mobileApplication->getRequestsForCurrentUser(5);

if (sizeOf($latestResults)) {
    foreach ($latestResults as $key => $userRequest) {
        unset($userRequest['dealType']['id']);
        if ($userRequest['dealType']['subtype2'] == 'n/a') unset($userRequest['dealType']['subtype2']);
        $latestResults[$key]['label'] = sprintf('%s, %s, %s', join(' > ', $userRequest['dealType']), $userRequest['industry']['industry'], $userRequest['country']['countryName']);          
    }
} 
//ini_set('display_errors', 1);
//error_reporting(E_ALL);
?>

Ext.setup({
    icon: 'icon.png',
    tabletStartupScreen: 'tablet_startup.png',
    phoneStartupScreen: 'phone_startup.png',
    glossOnIcon: false,
    onReady: function() {
    var myMask = new Ext.LoadMask(Ext.getBody(), {msg:"Please wait..."});
        var formBase = {
            id: 'chooseForm',
            scroll: 'vertical',
            url   : 'ajax/mobile/doRequest.php?type=userLogin',
            standardSubmit : false,
            items: [
                    {
                        xtype: 'panel',
                        layout: 'hbox',
                        titleCollapse: true,
                        autoWidth: true,
                        style: 'text-align:center; margin-top:5px',
                        height: 25,
                        html: 'Use 2 Weeks now ',
                    },{
                        xtype: 'fieldset',
                        items: [
                        {
                            xtype: 'hiddenfield',
                            id: 'industryToSend'
                        },{
                            xtype: 'hiddenfield',
                            id: 'countryToSend'
                        },{
                            xtype: 'hiddenfield',
                            id: 'dealTypeToSend'
                        },{
                            xtype: 'button',
                            text: 'Choose a Country',
                            name: 'chooseCountry',
                            id: 'chooseCountry',
                            style: 'margin-top:12px',
                            handler: function() {
                                Ext.ComponentMgr.get('mainPanel').setActiveItem('countrySelectionPanel', {type: 'slide', direction: 'left'}); 
                         
                            }
                         },{
                            xtype: 'button',
                            text: 'Choose Industry',
                            name: 'chooseIndustry',
                            id: 'chooseIndustry',
                            style: 'margin-top:12px',
                            disabled: true,
                            handler: function() {
                                Ext.ComponentMgr.get('mainPanel').setActiveItem('industrySelectionPanel', {type: 'slide', direction: 'left'}); 
                            }
                         }, {
                            xtype: 'button',
                            text: 'Choose Meeting Type',
                            name: 'chooseDealType',
                            id: 'chooseDealType',
                            style: 'margin-top:12px',
                            disabled: true,
                            handler: function() {
                                dealTypeStore.load();
                                Ext.ComponentMgr.get('mainPanel').setActiveItem('dealTypeSelectionPanel', {type: 'slide', direction: 'left'}); 
                         
                            }
                         },{
                            xtype: 'button',
                            text: 'Help Me Prepare',
                            ui: 'farward',
                            name: 'helpMePrepare',
                            id: 'helpMePrepare',
                            style: 'margin-top:12px',
                            disabled: true,
                            handler: function() {
                            
                                //console.log(Ext.ComponentMgr.get('dealTypeToSend'));
                                //console.log(Ext.ComponentMgr.get('industryToSend'));
                                //console.log(Ext.ComponentMgr.get('countryToSend'));
                                // Basic mask:
                                var myMask = new Ext.LoadMask(Ext.getBody(), {msg:"Please wait..."});
                                Ext.ComponentMgr.get('mainPanel').setActiveItem('resultsPanel', {type: 'slide', direction: 'left'});
                                myMask.show();                                
                                Ext.Ajax.request({
                                    url : 'ajax/mobile/doRequest.php?type=getResult' , 
                                    params : { 
                                        country : Ext.ComponentMgr.get('countryToSend').getValue(),
                                        industry : Ext.ComponentMgr.get('industryToSend').getValue(),
                                        dealType : Ext.ComponentMgr.get('dealTypeToSend').getValue()
                                    },
                                    method: 'POST',
                                    success: function ( result, request ) {
                                        Ext.ComponentMgr.get('resultsPanel').update(result.responseText, true);
                                        Ext.ComponentMgr.get('resultsPanel').doComponentLayout();
                                        elems = Ext.get('resultsPanel').query("a.link");
                                        Ext.each(elems, function(elem, index) {
                                            var obj = Ext.get(this);
                                            obj.on('singletap', function(){
                                            Ext.ComponentMgr.get('mainPanel').setActiveItem('detailsPanel', {type: 'slide', direction: 'left'});
                                            myMask.show();
                                            Ext.Ajax.request({
                                                url : 'ajax/mobile/doRequest.php?type=getDetails&dealId=' + obj.getAttribute('id') , 
                                                method: 'GET',
                                                success: function ( result, request ) {
                                                    Ext.ComponentMgr.get('detailsPanel').update(result.responseText, true);
                                                    myMask.hide();
                                                },
                                                failure: function ( result, request) { 
                                                    alert('Failed', result.responseText); 
                                                } 
                                            });
                                          });
                                        });                                    
                                        
                                        myMask.hide();
                                    },
                                    failure: function ( result, request) { 
                                        alert('Failed', result.responseText); 
                                    } 
                                });

                                                        
                                 
                            }
                         }


                        <?php if (sizeOf($latestResults)) { ?>
                         ,{
                            html: 'Your most recent "2 Weeks Now": <br /> <?php foreach ($latestResults as $result) { echo sprintf('<a href="#" class="link" id="%d" onclick="alert(\"aaaaa\")> %s </a><br />', $result['id'], $result['label']); }?>',
                            style: 'text-align:center; margin-top:12px',
                            listeners: {
                                afterrender: function(c){   
                                    elems = Ext.get(c.el).query('a.link');
                                    var myMask = new Ext.LoadMask(Ext.getBody(), {msg:"Please wait..."});
                                    Ext.each(elems, function(elem, index) {
                                            var obj = Ext.get(this);
                                            //console.log(obj);
                                            obj.on('click', function(c) {
                                                 //console.log('should be hidding now');
                                                myMask.show();
                                                Ext.Ajax.request({
                                                    url : 'ajax/mobile/doRequest.php?type=getResult' , 
                                                    params : { 
                                                        requestId : obj.getAttribute('id')
                                                    },
                                                    method: 'GET',
                                                    success: function ( result, request ) {
                                                        Ext.ComponentMgr.get('resultsPanel').update(result.responseText, true);
                                                        Ext.ComponentMgr.get('resultsPanel').doComponentLayout();
                                                        elems = Ext.get('resultsPanel').query("a.link");
                                                        Ext.ComponentMgr.get('mainPanel').setActiveItem('resultsPanel');
                                                        myMask.hide();
                                                        Ext.each(elems, function(elem, index) {
                                                            var obj = Ext.get(this);
                                                            obj.on('singletap', function(){
                                                                Ext.ComponentMgr.get('mainPanel').setActiveItem('detailsPanel', {type: 'slide', direction: 'left'});
                                                                myMask.show();
                                                                Ext.Ajax.request({
                                                                    url : 'ajax/mobile/doRequest.php?type=getDetails&dealId=' + obj.getAttribute('id') , 
                                                                    method: 'GET',
                                                                    success: function ( result, request ) {
                                                                        Ext.ComponentMgr.get('detailsPanel').update(result.responseText, true);
                                                                        myMask.hide();
                                                                    },
                                                                    failure: function ( result, request) { 
                                                                        myMask.hide();
                                                                        alert('Failed', result.responseText);
                                                                    } 
                                                                }); // ajax getDetails&dealId
                                                            }); //single tap
                                                        }); //each                                                     
                                                    },
                                                    failure: function ( result, request) { 
                                                        alert('Failed', result.responseText);
                                                        myMask.hide();
                                                    }                                        
                                                }); //ajax request doRequest.php?type=getResult   
                                                
                                            }) //obj on click
                                    })
                                }
                            }
                         }<?php } ?>]                         
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
                    window.location = '2WeeksNow.php?' + Math.random ();
                },
                exception : function(form, result){
                    //console.log('failure', Ext.toArray(arguments));
                    //console.log(result.errors);
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
            }
        };  
        
        form = new Ext.form.FormPanel(formBase);

        Ext.regModel('DealTypeListModel', {
            fields: [{name: 'text'}, {name: 'id'}]
        });
        
        var dealTypeStore = new Ext.data.TreeStore({
            model: 'DealTypeListModel',
            autoLoad: false,
            proxy: {
                type: 'ajax',
                url: 'ajax/mobile/doRequest.php?type=getAllMeetingTypes',
                reader: {
                    type: 'tree',
                    root: 'items'
                } 
            }
        });
        
        var dealTypeNestedList = new Ext.NestedList({
            fullscreen: false,
            title: 'Deal Type',
            displayField: 'text',
            store: dealTypeStore,
            flex: "1"
        });

        dealTypeNestedList.on('leafitemtap', function(subList, subIdx, el, e) {
                        var ds = subList.getStore(),
                        r  = ds.getAt(subIdx);
                        //console.log(r);
                        Ext.ComponentMgr.get('chooseDealType').setText(r.data.text);
                        Ext.ComponentMgr.get('dealTypeToSend').setValue(r.data.id);
                        Ext.ComponentMgr.get('helpMePrepare').enable();
                        mainPanel.setActiveItem('chooseForm');
        });

        dealTypeSelectionPanel = new Ext.Panel({
            id: 'dealTypeSelectionPanel',
            layout: 'fit',
            items: [
                dealTypeNestedList
            ]
        })
         
        Ext.regModel('Industry', {
            fields: ['id', 'industry', 'sector']
        });

        var industrySelectionStore = new Ext.data.Store({
            model  : 'Industry',
            sorters: 'sector',

            getGroupString : function(record) {
                return record.get('sector');
            },

            data: [{id:"1",sector:"Basic Materials",industry:"Agriculture"},{id:"4",sector:"Basic Materials",industry:"Coal"},{id:"7",sector:"Basic Materials",industry:"Forest Products & Paper"},{id:"8",sector:"Basic Materials",industry:"Iron & Steel"},{id:"9",sector:"Basic Materials",industry:"Mining"},{id:"10",sector:"Communications",industry:"Advertising"},{id:"12",sector:"Communications",industry:"Computers"},{id:"13",sector:"Communications",industry:"Electronics"},{id:"14",sector:"Communications",industry:"Internet"},{id:"11",sector:"Communications",industry:"IT Services"},{id:"15",sector:"Communications",industry:"Media"},{id:"16",sector:"Communications",industry:"Semiconductors"},{id:"17",sector:"Communications",industry:"Software"},{id:"18",sector:"Communications",industry:"Telecommunications"},{id:"19",sector:"Consumer, Cyclical",industry:"Advertising"},{id:"20",sector:"Consumer, Cyclical",industry:"Airlines"},{id:"21",sector:"Consumer, Cyclical",industry:"Airports"},{id:"22",sector:"Consumer, Cyclical",industry:"Apparel"},{id:"23",sector:"Consumer, Cyclical",industry:"Auto Manufacturers"},{id:"24",sector:"Consumer, Cyclical",industry:"Auto Parts"},{id:"29",sector:"Consumer, Cyclical",industry:"Cosmetics & Personal Care"},{id:"33",sector:"Consumer, Cyclical",industry:"Home Builders"},{id:"35",sector:"Consumer, Cyclical",industry:"Household Products"},{id:"37",sector:"Consumer, Cyclical",industry:"Leisure Time"},{id:"39",sector:"Consumer, Cyclical",industry:"Retail"},{id:"40",sector:"Consumer, Cyclical",industry:"Transportation"},{id:"42",sector:"Consumer, Non-Cyclical",industry:"Beverages"},{id:"43",sector:"Consumer, Non-Cyclical",industry:"Biotechnology"},{id:"45",sector:"Consumer, Non-Cyclical",industry:"Commercial Services"},{id:"47",sector:"Consumer, Non-Cyclical",industry:"Distribution & Wholesale"},{id:"48",sector:"Consumer, Non-Cyclical",industry:"Electronics"},{id:"49",sector:"Consumer, Non-Cyclical",industry:"Food"},{id:"50",sector:"Consumer, Non-Cyclical",industry:"Healthcare Products"},{id:"51",sector:"Consumer, Non-Cyclical",industry:"Healthcare Services"},{id:"52",sector:"Consumer, Non-Cyclical",industry:"Pharmaceuticals"},{id:"55",sector:"Consumer, Non-Cyclical",industry:"Tobacco"},{id:"57",sector:"Diversified",industry:"Holding Companies"},{id:"58",sector:"Energy",industry:"Alternative Energy"},{id:"64",sector:"Energy",industry:"Oil & Gas"},{id:"65",sector:"Energy",industry:"Pipelines"},{id:"66",sector:"Finance",industry:"Banks"},{id:"69",sector:"Finance",industry:"Development Finance"},{id:"70",sector:"Finance",industry:"Diversified Financial Services"},{id:"72",sector:"Finance",industry:"Insurance"},{id:"74",sector:"Finance",industry:"Investment Companies"},{id:"75",sector:"Finance",industry:"Investment Fund"},{id:"76",sector:"Finance",industry:"Real Estate"},{id:"77",sector:"Finance",industry:"REITS"},{id:"78",sector:"Finance",industry:"Venture Capital"},{id:"80",sector:"Industrials",industry:"Aerospace & Defense"},{id:"81",sector:"Industrials",industry:"Building Materials"},{id:"82",sector:"Industrials",industry:"Chemicals"},{id:"83",sector:"Industrials",industry:"Commercial Services"},{id:"84",sector:"Industrials",industry:"Electrical Components"},{id:"85",sector:"Industrials",industry:"Engineering & Construction"},{id:"86",sector:"Industrials",industry:"Environmental Control"},{id:"87",sector:"Industrials",industry:"Machinery"},{id:"88",sector:"Industrials",industry:"Manufacturing"},{id:"89",sector:"Industrials",industry:"Metals"},{id:"91",sector:"Industrials",industry:"Packaging & Containers"},{id:"94",sector:"Industrials",industry:"Shipbuilding"},{id:"96",sector:"Sovereign",industry:"Agency"},{id:"97",sector:"Sovereign",industry:"Municipal\/ State\/ Province"},{id:"98",sector:"Sovereign",industry:"Sovereign"},{id:"99",sector:"Utilities",industry:"Electric"},{id:"100",sector:"Utilities",industry:"Gas"},{id:"101",sector:"Utilities",industry:"Water"}]
        });

        var industrySelectionList = new Ext.List({
            fullscreen: false,
            itemTpl : '{industry}',
            grouped : true,
            indexBar: false,
            
            store: industrySelectionStore
        });
        
        industrySelectionPanel = new Ext.Panel({
            id: 'industrySelectionPanel',
            layout: 'fit',
            items: [
                industrySelectionList
            ]
        })
                
        industrySelectionList.on('itemtap', function(subList, subIdx, el, e) {
                        var ds = subList.getStore(),
                        r  = ds.getAt(subIdx);
                        Ext.ComponentMgr.get('chooseIndustry').setText(r.data.industry);
                        Ext.ComponentMgr.get('industryToSend').setValue(r.data.id);
                        Ext.ComponentMgr.get('chooseDealType').enable();
                        mainPanel.setActiveItem('chooseForm');
                          
        });
              
         Ext.regModel('Country', {
            fields: ['id', 'name']
        });

        var countrySelectionStore = new Ext.data.Store({
            model  : 'Country',
              data: [{id: 87, name : 'Angola' },{id: 41, name : 'Argentina' },{id: 5, name : 'Australia' },{id: 42, name : 'Austria' },{id: 94, name : 'Azerbaijan' },{id: 98, name : 'Bahamas' },{id: 43, name : 'Bahrain' },{id: 68, name : 'Bangladesh' },{id: 105, name : 'Belarus' },{id: 14, name : 'Belgium' },{id: 96, name : 'Belize' },{id: 46, name : 'Bermuda' },{id: 4, name : 'Brazil' },{id: 55, name : 'British Virgin Islands' },{id: 100, name : 'Bulgaria' },{id: 13, name : 'Canada' },{id: 27, name : 'Cayman Islands' },{id: 77, name : 'Channel Islands' },{id: 95, name : 'Chile' },{id: 22, name : 'China' },{id: 36, name : 'Colombia' },{id: 99, name : 'Costa Rica' },{id: 57, name : 'Croatia' },{id: 109, name : 'Cuba' },{id: 60, name : 'Cyprus' },{id: 33, name : 'Czech Republic' },{id: 35, name : 'Denmark' },{id: 10, name : 'Egypt' },{id: 67, name : 'El Salvador' },{id: 101, name : 'Estonia' },{id: 48, name : 'Finland' },{id: 20, name : 'France' },{id: 91, name : 'Georgia' },{id: 7, name : 'Germany' },{id: 102, name : 'Ghana' },{id: 38, name : 'Greece' },{id: 64, name : 'Guernsey' },{id: 86, name : 'Guinea' },{id: 18, name : 'Hong Kong' },{id: 30, name : 'Hungary' },{id: 83, name : 'Iceland' },{id: 40, name : 'India' },{id: 50, name : 'Indonesia' },{id: 75, name : 'Iran' },{id: 97, name : 'Iraq' },{id: 29, name : 'Ireland' },{id: 53, name : 'Israel' },{id: 21, name : 'Italy' },{id: 107, name : 'Ivory Coast' },{id: 49, name : 'Jamaica' },{id: 9, name : 'Japan' },{id: 79, name : 'Jersey' },{id: 63, name : 'Kazakhstan' },{id: 85, name : 'Kenya' },{id: 74, name : 'Kuwait' },{id: 93, name : 'Latvia' },{id: 56, name : 'Lebanon' },{id: 59, name : 'Lithuania' },{id: 16, name : 'Luxembourg' },{id: 65, name : 'Macao' },{id: 11, name : 'Malaysia' },{id: 66, name : 'Marshall Islands' },{id: 37, name : 'Mexico' },{id: 62, name : 'Morocco' },{id: 82, name : 'Mozambique' },{id: 108, name : 'Namibia' },{id: 8, name : 'Netherlands' },{id: 58, name : 'New Zealand' },{id: 81, name : 'Nigeria' },{id: 26, name : 'Norway' },{id: 106, name : 'Oman' },{id: 89, name : 'Pakistan' },{id: 69, name : 'Panama' },{id: 71, name : 'Papua New Guinea' },{id: 80, name : 'Peru' },{id: 34, name : 'Philippines' },{id: 45, name : 'Poland' },{id: 3, name : 'Portugal' },{id: 88, name : 'Puerto Rico' },{id: 1, name : 'Qatar' },{id: 52, name : 'Romania' },{id: 17, name : 'Russia' },{id: 44, name : 'Saudi Arabia' },{id: 104, name : 'Serbia' },{id: 39, name : 'Singapore' },{id: 25, name : 'Slovak Republic' },{id: 54, name : 'Slovenia' },{id: 32, name : 'South Africa' },{id: 24, name : 'South Korea' },{id: 19, name : 'Spain' },{id: 72, name : 'Sri Lanka' },{id: 92, name : 'Sudan' },{id: 15, name : 'Supra-national' },{id: 23, name : 'Sweden' },{id: 6, name : 'Switzerland' },{id: 47, name : 'Taiwan' },{id: 51, name : 'Thailand' },{id: 78, name : 'Trinidad & Tobago' },{id: 90, name : 'Tunisia' },{id: 31, name : 'Turkey' },{id: 70, name : 'Ukraine' },{id: 28, name : 'United Arab Emirates' },{id: 12, name : 'United Kingdom' },{id: 2, name : 'United States' },{id: 76, name : 'Uruguay' },{id: 73, name : 'Venezuela' },{id: 61, name : 'Vietnam' },{id: 103, name : 'Yemen' },{id: 84, name : 'Zambia' }]
        });

        var countrySelectionList = new Ext.List({
            fullscreen: false,
            
            itemTpl : '{name}',
            grouped : false,
            indexBar: false,
            
            store: countrySelectionStore
        });
        
        countrySelectionPanel = new Ext.Panel({
            id: 'countrySelectionPanel',
            layout: 'fit',
            items: [
                countrySelectionList
            ]
        });  
              
        
        countrySelectionList.on('itemtap', function(subList, subIdx, el, e) {
                        var ds = subList.getStore(),
                        r  = ds.getAt(subIdx);
                        Ext.ComponentMgr.get('chooseCountry').setText(r.data.name);
                        Ext.ComponentMgr.get('countryToSend').setValue(r.data.id);
                        Ext.ComponentMgr.get('chooseIndustry').enable();
                        mainPanel.setActiveItem('chooseForm');
        });
        
         resultsPanel = new Ext.Panel({
            id: 'resultsPanel',
            layout: 'fit',
            html: '',
            scroll: 'vertical',
            dockedItems: [{
                xtype: 'toolbar',
                ui: 'light',
                  items: [
                     {
                    text: 'Back',
                    ui: 'back',
                    iconAlign: 'top',
                    iconMask: false,
                    handler: function() {
                        mainPanel.setActiveItem('chooseForm');
                    }
                }],
                dock: 'top',
                layout: {
                    pack: 'left'
                }                
            }]
        });
         
        detailsPanel = new Ext.Panel({
            id: 'detailsPanel',
            layout: 'fit',
            html: '',
            scroll: 'vertical',
            dockedItems: [{
                xtype: 'toolbar',
                ui: 'light',
                  items: [
                     {
                    text: 'Back to my search',
                    ui: 'back',
                    iconAlign: 'top',
                    iconMask: false,
                    handler: function() {
                        mainPanel.setActiveItem('resultsPanel');
                    }
                }],
                dock: 'top',
                layout: {
                    pack: 'left'
                }                
            }]
        });
   
        var mainPanel = new Ext.Panel({
            id: 'mainPanel',
            fullscreen : true,
            layout : 'card',
            cardAnimation : 'slide',
            items: [form, dealTypeSelectionPanel, industrySelectionPanel, countrySelectionPanel, resultsPanel, detailsPanel],
            dockedItems: [{
                xtype: 'toolbar',
                ui: 'light',
                items: [
                {
                    xtype : "container", 
                    html : '<img src="images/deal_data_logo.png" style="width: 100px;" />', 
                    flex: 1 
                    
                }, { 
                    xtype : "spacer", 
                }, {
                    text: 'Logout',
                    iconAlign: 'bottom',
                    iconMask: false,
                    width: 100,
                    handler: function() {
                       var myMask = new Ext.LoadMask(Ext.getBody(), {msg:"Please wait..."});
                       myMask.show();
                       Ext.Ajax.request({
                            url : 'ajax/mobile/doRequest.php?type=logOut' , 
                            method: 'GET',
                            success: function ( result, request ) {
                                window.location.href = '2WeeksNow.php?' + Math.random ()
                            },
                            failure: function ( result, request) { 
                                //alert('Failed', result.responseText); 
                            } 
                        });
                    }
                }],
                dock: 'bottom',
                layout: {
                    pack: 'right'
                }                
            }]            
        });
        
    }
});
