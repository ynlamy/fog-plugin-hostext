$(function() {
    checkboxToggleSearchListPages();
    validatorOpts = {
        submitHandler: submithandlerfunc,
        rules: {
            name: {
                required: true,
                minlength: 1,
                maxlength: 255
            },
            url: {
                required: true,
                minlength: 1,
                maxlength: 255,
                regex: /^(http(s)?:\/\/)?(www\.)?[a-z0-9]+([\-\.]{1}[a-z0-9]+)*\.[a-z]{2,5}(:[0-9]{1,5})?(\/.*)?$/
            },
            variable: {
                required: true
            }
        }
    };
    setupTimeoutElement('#add, #updategen', '.hostextinput-name, .hostextinput-url, .hostextselect-variable', 1000);
    $('.action-boxes').on('submit',function() {
        var checked = $('input.toggle-action:checked');
        var HostextIDArray = new Array();
        for (var i = 0,len = checked.size();i < len;i++) {
            HostextIDArray[HostextIDArray.length] = checked.eq(i).attr('value');
        }
        $('input[name="HostextIDArray"]').val(HostextIDArray.join(','));
    });
});
