<!-- ================== BEGIN BASE JS ================== -->

<script src="{{asset('/js/app.min.js')}}"></script>
<script src="{{asset('/assets/js/theme/default.js')}}"></script>
<script>

    
var RFC =  (function () {

var modulo = {};

/**
* Valida que la cadena proporcionada se apegue a la definición de la CURP
*/
modulo.valida = function (rfc,aceptarGenerico = true) {
    const re = /^([A-ZÑ&]{3,4}) ?(?:- ?)?(\d{2}(?:0[1-9]|1[0-2])(?:0[1-9]|[12]\d|3[01])) ?(?:- ?)?([A-Z\d]{2})([A\d])$/;
    var   validado = rfc.match(re);

    if (!validado)  //Coincide con el formato general del regex?
        return false;

    //Separar el dígito verificador del resto del RFC
    const digitoVerificador = validado.pop(),
        rfcSinDigito      = validado.slice(1).join(''),
        len               = rfcSinDigito.length,

    //Obtener el digito esperado
        diccionario       = "0123456789ABCDEFGHIJKLMN&OPQRSTUVWXYZ Ñ",
        indice            = len + 1;
    var   suma,
        digitoEsperado;

    if (len == 12) suma = 0
        else suma = 481; //Ajuste para persona moral

        for(var i=0; i<len; i++){
            suma += diccionario.indexOf(rfcSinDigito.charAt(i)) * (indice - i);
        }
        digitoEsperado = 11 - suma % 11;
        if (digitoEsperado == 11) digitoEsperado = 0;
        else if (digitoEsperado == 10) digitoEsperado = "A";

        //El dígito verificador coincide con el esperado?
        // o es un RFC Genérico (ventas a público general)?
        if ((digitoVerificador != digitoEsperado)
        && (!aceptarGenerico || rfcSinDigito + digitoVerificador != "XAXX010101000"))
            return false;
        else if (!aceptarGenerico && rfcSinDigito + digitoVerificador == "XEXX010101000")
            return false;
        return rfcSinDigito + digitoVerificador;
    }
    return modulo;
}());

    function validaRFC(rfc){
    var rfc = rfc.trim().toUpperCase();


    if(rfc.length == 0){
        return;
    }
    if (RFC.valida(rfc)) {
        return true;
    }

    swal({title: 'Error',text: 'El RFC no es valido',icon: 'warning',});   
}


//validacion curp

var CURP = (function () {

    var modulo = {};

    /**
    * Valida que la cadena proporcionada se apegue a la definición de la CURP
    */
    modulo.valida = function (curp) {
        var reg = "";
        if(curp.length == 18)
        {
            var digito = this.verifica(curp);

            reg = /[A-Z]{4}\d{6}[HM][A-Z]{2}[B-DF-HJ-NP-TV-Z]{3}[A-Z0-9][0-9]/;

            if(curp.search(reg))
            {
                return false;
            }

            if(!(parseInt(digito) == parseInt(curp.substring(17,18))))
            {
                return false;
            }
                return true;
        }
        else
        {
                return false;
        }
    };

    /**
    * Comprueba el dígito verificador mediante el algoritmo de LUHN "tropicalizado"
    */
    modulo.verifica = function(curp){
        var segRaiz      = curp.substring(0,17);
        var chrCaracter  = "0123456789ABCDEFGHIJKLMNÑOPQRSTUVWXYZ";
        var intFactor    = new Array(17);
        var lngSuma      = 0.0;
        var lngDigito    = 0.0;

        for(var i=0; i<17; i++)
        {
            for(var j=0;j<37; j++)
            {
                if(segRaiz.substring(i,i+1)==chrCaracter.substring(j,j+1))
                {
                    intFactor[i]=j;
                }
            }
        }
        for(var k = 0; k < 17; k++)
        {
            lngSuma= lngSuma + ((intFactor[k]) * (18 - k));
        }
        lngDigito= (10 - (lngSuma % 10));

        if(lngDigito==10)
        {
            lngDigito=0;
        }

        return lngDigito;
    };

    return modulo;
}());

function validaCURP(curp){
    if(curp.length == 0){
        return false;
    }
    if(CURP.valida(curp)){
    //   document.getElementById('msg').innerText = 'CURP Válida';
        return true;
    }

    swal({title: 'Error',text: 'La curp no es valida',icon: 'warning',});   
}
/**
  *  Funcion para calcular edad
**/
function Edad(FechaNacimiento) {
    var fechaNace = new Date(dateFormat(FechaNacimiento));
    var fechaActual = new Date()

    var mes = fechaActual.getMonth();
    var dia = fechaActual.getDate();
    var año = fechaActual.getFullYear();

    fechaActual.setDate(dia);
    fechaActual.setMonth(mes);
    fechaActual.setFullYear(año);
    edad = Math.floor(((fechaActual - fechaNace) / (1000 * 60 * 60 * 24) / 365));

    return edad;
}

</script>
<!-- ================== END BASE JS ================== -->

@stack('scripts')
