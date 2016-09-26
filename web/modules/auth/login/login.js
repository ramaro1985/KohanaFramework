Ext.onReady(function () {
    var submitBtn = Ext.get('submitBtn');
    submitBtn.on({
        'click': {
            fn: onClick
        },
        'mouseover': {
            fn: function () {
                submitBtn.addClass('qo-submit-over');
            }
        },
        'mouseout': {
            fn: function () {
                submitBtn.removeClass('qo-submit-over');
            }
        }
    });

    function hideLoginFields() {
        Ext.get('field1-label').setDisplayed('none');
        Ext.get('field1').setDisplayed('none');
        Ext.get('field2-label').setDisplayed('none');
        Ext.get('field2').setDisplayed('none');
    }

    function onClick() {
        var usernameField = Ext.get("field1");
        var username = usernameField.dom.value;
        var pwdField = Ext.get("field2");
        var pwd = pwdField.dom.value;

        if (validate(username) === false) {
            Ext.Msg.alert('Validation Error', 'Your username is required');
            return;
        }

        if (validate(pwd) === false) {
            Ext.Msg.alert('Validation Error', 'Your password is required');
            return;
        }

        Ext.Ajax.request({
            url: '/auth/login',
            params: {
                username: username,
                password: pwd
            },
            success: function (o) {
                if (typeof o == 'object') {
                    var d = Ext.decode(o.responseText);

                    if (typeof d == 'object') {
                        if (d.success == true) {
                            Ext.Msg.show({
                                title:d.msg.title,
                                msg: d.msg.msg,
                                buttons: Ext.Msg.OK,
                                icon: Ext.MessageBox.INFO,
                                fn: function() {
                                    window.location = '/';
                                }
                            });
                        } else {
                            if (d.msg) {
                                Ext.Msg.show({
                                    title:d.msg.title,
                                    msg: d.msg.msg,
                                    buttons: Ext.Msg.OK,
                                    icon: Ext.MessageBox.ERROR
                                });
                            } else {
                                Ext.Msg.alert('Errors encountered on the server.', 'Error');
                            }
                        }
                    }
                }
            },
            failure: function () {
                alert('Lost connection to server.');
            }
        });
    }

    function validate(field) {
        if (field === '') {
            return false;
        }
        return true;
    }
});