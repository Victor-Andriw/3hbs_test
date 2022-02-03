// import something here
import Vue from 'vue';
import { extend } from 'vee-validate';
import { required, email, numeric, min, max, length,digits} from 'vee-validate/dist/rules';
import { ValidationProvider,ValidationObserver} from 'vee-validate';

// Add the required rule
extend('required', {
    ...required,
    message: 'required field'
});
  
extend('min', {
    ...min,
    message: 'The field must have at least {length} characters'
});
  
extend('max', {
    ...max,
    message: 'The field must have maximum {length} characters'
});
  
extend('digits', {
    ...digits,
    message: 'Field must have {length} digits'
});
  
extend('email', {
    ...email,
    message: 'The field must be a valid email'
});
  
extend("decimal",{
    validate: (value, { decimals = '*', separator = '.' } = {}) => {
        if (value === null || value === undefined || value === '') {
            return {
              valid: false
            };
        }
        if (Number(decimals) === 0) {
            return {
                valid: /^-?\d*$/.test(value),
            };
        }
  
        const regexPart = decimals === '*' ? '+' : `{1,${decimals}}`;
        const regex = new RegExp(`^[-+]?\\d*(\\${separator}\\d${regexPart})?([eE]{1}[-]?\\d+)?$`);
        return regex.test(value);
  
    },
    message: "The field only accepts numbers with a decimal point"
});
  
extend("numeric",{
    ...numeric,
    message: "The field only accepts numbers"
});
  
extend("length",{
    ...length,
    message: "The field must have {length} characters"
});  

Vue.component('ValidationProvider', ValidationProvider);
Vue.component('ValidationObserver', ValidationObserver);