<html>
  <body>

    <div id="app">
            <button v-on:click="handlePayButton">Bayar</button>
    </div>
    <script src="https://cdn.jsdelivr.net/npm/vue/dist/vue.js"></script>

    <script src="https://cdn.jsdelivr.net/npm/vue-resource@1.5.1"></script>
    
<script src="https://app.sandbox.midtrans.com/snap/snap.js" data-client-key="SB-Mid-client-Pn52YOpKa5DnK-A7"></script>
    
<script>
    var Vue = new Vue({
        el:'#app',
        data:function(){
            return {
                data_midtrans:{
                    'transaction_details' :{
                        'order_id': 'order-anjay-131342',
                        'gross_amount': 500000
                    },
                    'costumer_detail':{
                        'first_name':'Fitra',
                        'last_name':'Ashari',
                        'email': 'fitra.drive@gmail',
                        'phone': '08123123234',
                    }
                }
            }
        },
        methods: {
            handlePayButton:function (event){
                // console.log('masuk');
                this.$http.post('/api/generate',{data:this.data_midtrans}).then(response=>{
                    snap.pay(response.data.data.token)
                    // console.log(response.data.data);
                }, response=>{
                    console.log('Error '+response)
                })
            }
        },
    })
</script>
  </body>
</html>