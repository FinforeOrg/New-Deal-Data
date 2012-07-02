Ext.setup({
    icon: 'icon.png',
    tabletStartupScreen: 'tablet_startup.png',
    phoneStartupScreen: 'phone_startup.png',
    glossOnIcon: false,
    onReady: function() {

        var form;
        
        Ext.regModel('userLogin', {
            fields: [
                {name: 'name',     type: 'string'},
                {name: 'password', type: 'password'},
            ]
        });
        
        var formBase = {
            scroll: 'vertical',
            url   : 'ajax/mobile/doRequest.php?type=userLogin',
            standardSubmit : false,
            items: [
					{
						xtype: 'panel',
						html: '<img src="images/deal_data_logo.png" style="margin: 0 auto;"/>',
						titleCollapse: true,
						autoWidth: true,
						style: 'text-align:center'
					},
					{
				    xtype: 'fieldset',
                    defaults: {
                        required: true,
                        labelAlign: 'left',
                        labelWidth: '40%'
                    },
                    items: [
                    {
                        xtype: 'emailfield',
                        name : 'username',
                        label: 'Username',
                        useClearIcon: true,
                        autoCapitalize : false
                    }, {
                        xtype: 'passwordfield',
                        name : 'password',
                        label: 'Password',
                        useClearIcon: false
                    } , {
						xtype: 'button',
						text: 'Login',
						type: 'submit',
						style: 'margin-top: 10px;',
						handler: function() {
							form.submit({
								waitMsg : {message:'Submitting', cls : 'demos-loading'}
							});
						}						
					}]
                },
				{
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