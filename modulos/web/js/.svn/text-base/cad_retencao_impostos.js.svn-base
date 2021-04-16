jQuery(document).ready(function(){

   jQuery('#prsriiss').maskMoney({
        symbol:'',
        thousands:'.',
        decimal:',',
        symbolStay: false,
        showSymbol:false,
        precision:2,
        allowZero: true
    });

   jQuery('#prsripis').maskMoney({
        symbol:'',
        thousands:'.',
        decimal:',',
        symbolStay: false,
        showSymbol:false,
        precision:2,
        allowZero: true
    });

   jQuery('#prsricofins').maskMoney({
        symbol:'',
        thousands:'.',
        decimal:',',
        symbolStay: false,
        showSymbol:false,
        precision:2,
        allowZero: true
    });

    jQuery('#prsrivalor_chip').maskMoney({
        symbol:'',
        thousands:'.',
        decimal:',',
        symbolStay: false,
        showSymbol:false,
        precision:2,
        allowZero: true
    });

    jQuery("#prsriiss, #prsripis, #prsricofins, #prsrivalor_chip").on('paste',function(){
        var id = jQuery(this).attr('id');
        var maxlength = jQuery(this).attr('maxlength');

        setTimeout(function(){

            var v = jQuery("#"+id).val();
            var vMasc = maskValue(v);
            var nV = v;

            if (vMasc.length > maxlength) {
                nV = "";
                var maxChar = (maxlength - (vMasc.length - maxlength));
                var vArray = v.split("");
                var i = 0;
                for ( i ; i <= maxChar ; i++) {
                    nV += vArray[i];
                }
            }

            jQuery("#"+id).val( maskValue(nV) );

        },10);
    });

    jQuery("#prsriiss, #prsripis, #prsricofins, #prsrivalor_chip").on('keyup',function(){
        var id = jQuery(this).attr('id');
        jQuery("#"+id).val( maskValue(jQuery("#"+id).val()) );
    });

    settings = {};
    settings.allowNegative = false;
    settings.decimal = ',';
    settings.precision = 2;
    settings.thousands = '.';

    function maskValue(v) {

        var strCheck = '0123456789';
        var len = v.length;
        var a = '', t = '', neg='';

        if(len!=0 && v.charAt(0)=='-'){
            v = v.replace('-','');
            if(settings.allowNegative){
                neg = '-';
            }
        }

        for (var i = 0; i<len; i++) {
            if ((v.charAt(i)!='0') && (v.charAt(i)!=settings.decimal)) break;
        }

        for (; i<len; i++) {
            if (strCheck.indexOf(v.charAt(i))!=-1) a+= v.charAt(i);
        }

        var n = parseFloat(a);
        n = isNaN(n) ? 0 : n/Math.pow(10,settings.precision);
        t = n.toFixed(settings.precision);

        i = settings.precision == 0 ? 0 : 1;
        var p, d = (t=t.split('.'))[i].substr(0,settings.precision);
        for (p = (t=t[0]).length; (p-=3)>=1;) {
            t = t.substr(0,p)+settings.thousands+t.substr(p);
        }

        return (settings.precision>0)
        ? neg+t+settings.decimal+d+Array((settings.precision+1)-d.length).join(0)
        : neg+t;
    }

});