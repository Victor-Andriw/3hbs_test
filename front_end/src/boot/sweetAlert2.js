import Vue from 'vue'
import 'sweetalert2/dist/sweetalert2.min.css';

const options = {
    confirmButtonColor: '#1976D2',
    cancelButtonColor: '#ff7674',

};
  

Vue.prototype.$swal = require('sweetalert2')

Vue.prototype.$show_swal = async function(icon, title, info){
    await await new this.$swal({
      icon : icon,
      title : title,
      html : `<p>${info}</p>`,
      allowEscapeKey : false,
      allowOutsideClick : false,
    })
  }

  Vue.prototype.$question_swal = async function(info){
    return await new this.$swal({
      icon : 'question',
      title : 'Confirm',
      html : `<p>${info}</p>`,
      confirmButtonText : 'Yes',
      showCancelButton : true,
      cancelButtonText : 'No',
      allowEscapeKey : false,
      allowOutsideClick : false,
      allowEnterKey : false,
      reverseButtons : true
    }).then( (conf) => conf.value );
  }
  