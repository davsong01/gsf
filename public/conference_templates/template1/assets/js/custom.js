// const paymentForm = document.getElementById('paymentForm');

// paymentForm.addEventListener("submit", payWithPaystack, false);

// function payWithPaystack(e) {

//   e.preventDefault();


//   let handler = PaystackPop.setup({

//     key: 'pk_test_xxxxxxxxxx', // Replace with your public key

//     email: document.getElementById("email-address").value,

//     amount: document.getElementById("amount").value * 100,

//     ref: ''+Math.floor((Math.random() * 1000000000) + 1), // generates a pseudo-unique reference. Please replace with a reference you generated. Or remove the line entirely so our API will generate one for you

//     // label: "Optional string that replaces customer email"

//     onClose: function(){

//       alert('Window closed.');

//     },

//     callback: function(response){

//       let message = 'Payment complete! Reference: ' + response.reference;

//       alert(message);

//     }

//   });


//   handler.openIframe();

// }
const paymentForm = document.getElementById('paymentForm');
paymentForm.addEventListener("submit", payWithPaystack, false);

function payWithPaystack(e) {
    
    e.preventDefault();

    // let key= '{{ env('PAYSTACK_PUBLIC_KEY') }}'; // Replace with your public key
    let email= document.getElementById("email-address").value;
    let amount = document.getElementById("amount").value * 100;
    let name = document.getElementById("first-name").value;
    let phone = document.getElementById("phone").value;
    let gender = document.getElementById("gender").value;
    // let chapter = document.getElementById("chapterind").value;
    let type = 1;

    alert('here');
    // let temp = createTempDetails(email,amount,name,phone,gender,chapter,type);
    // console.log(temp);
    let handler = PaystackPop.setup({
        // ref: ''+Math.floor((Math.random() * 1000000000) + 1), // generates a pseudo-unique reference. Please replace with a reference you generated. Or remove the line entirely so our API will generate one for you
        ref: ref, // generates a pseudo-unique reference. Please replace with a reference you generated. Or remove the line entirely so our API will generate one for you
        // label: "Optional string that replaces customer email"
        key:key,
        email:email,
        amount:amount,
        onClose: function(){
            // alert('Window closed.');
        },

        callback: function(response){

        let message = 'Payment complete! Reference: ' + response.reference;

        alert(message);

        }

});


handler.openIframe();

}

function createTempDetails(email,amount){
    $.ajax({
        url: 'ajax-create-temp-details',
        type: "POST",
        data: {
            email: email,
            amount: amount,
        },
        // beforeSend: function(xhr){
        //     $('#api-full').prepend('LOADING...');
        //     xhr.setRequestHeader ("Authorization", "Basic {{ base64_encode('degodtest@gmail.com:311223') }}");
        // },
        success: function(res){
            return res;
            // var resp = res[0].full_response;
            // if(resp=='' || resp==null){
            //     resp = res[0].response_description;
            // }
            // $('#api-full').html(resp);

            // $.each(res, function(key, value){
            //     $('#the-requery-btns').prepend('<span>'+value.requestId+'</span><div><a id="in-query" valuer="'+value.requestId+'" valuex="'+value.api_id+'" class="btn btn-warning btn-xs purple"><i class="fa fa-credit-card"></i> query</a> <a class="btn btn-warning btn-xs purple" href="{{url(\Request::route()->getPrefix()."/api-requery-delivered")}}/'+value.id+'"><i class="fa fa-share"></i> Extract Token</a> <a id="in-log" value="'+value.id+'" valuer="'+btoa(value.full_response)+'" valuex="'+btoa(value.response_description)+'" class="btn btn-warning btn-xs purple"><i class="fa fa-credit-card"></i> View Log</a>');
            // });
        }
    });
}
  