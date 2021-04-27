/**
 * @name dateMin
 * @author Ruy Junior Ferreira Silva <ruyjfs@gmail.com>
 * @since 11/01/2017
 */
$3.validator.addMethod("dateMax", function(value, element, params) {
    var curDate = params;
    var inputDate = new Date(value);
    if (inputDate < curDate)
        return true;
    return false;
}, "Por favor, digite uma data valida!");
/**
 * @name dateMax
 * @author Ruy Junior Ferreira Silva <ruyjfs@gmail.com>
 * @since 11/01/2017
 */
$3.validator.addMethod("dateMin", function(value, element, params) {
    var curDate = params;
    var inputDate = new Date(value);
    if (inputDate > curDate)
        return true;
    return false;
}, "Por favor, digite uma data minima valida!");