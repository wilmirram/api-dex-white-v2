<!doctype html>
<html lang="pt-BR">
<head>
    <meta charset="UTF-8">
    <meta name="viewport"
          content="width=device-width, user-scalable=no, initial-scale=1.0, maximum-scale=1.0, minimum-scale=1.0">
    <meta http-equiv="X-UA-Compatible" content="ie=edge">
    <title>Paypal</title>
</head>
<body>

    <div id="ppplus">
    </div>

    <button
        type="submit"
        id="continueButton"> Pagar
    </button>

    <script src="https://www.paypalobjects.com/webstatic/ppplusdcc/ppplusdcc.min.js" type="text/javascript"></script>

    <script>

        let ppp;
        let payment_id;

        getCheckOut()
        async function getCheckOut()
        {

            let url = `/api/paypal-store/{{$id}}`;
            let response = await fetch(url, {
                method: 'POST',
                headers: {
                    "accept": "application/json",
                    "Content-Type": "application/json",
                }
            });
            let json = await response.json();
            ppp = await PAYPAL.apps.PPP({
                "approvalUrl": json.links[1].href,
                "placeholder": "ppplus",
                "mode": "sandbox",
                "payerEmail": "igorpc.tv@gmail.com",
                "payerFirstName": "Igor",
                "payerLastName": "Coutinho",
                "payerTaxId": "43669456813",
                "language": "pt_BR",
                "country": "BR"
            });
            payment_id = await json.id;
        }

        document.querySelector("#continueButton").addEventListener('click', (event) => {
            event.preventDefault();
            ppp.doContinue();
            if (window.addEventListener) {
                window.addEventListener("message", messageListener, false);
            } else if (window.attachEvent) {
                window.attachEvent("onmessage", messageListener);
            } else {
                throw new Error("Can't attach message listener");
            }
        });

        async function messageListener(event) {
            try {
                //this is how we extract the message from the incoming events, data format should look like {"action":"inlineCheckout","checkoutSession":"error","result":"missing data in the credit card form"}
                var data = JSON.parse(event.data);
                if (data.result.state == "APPROVED"){
                    let response = await fetch("/api/paypal-confirm-payment", {
                        method: 'POST',
                        headers: {
                            "accept": "application/json",
                            "Content-Type": "application/json"
                        },
                        body: JSON.stringify({
                            payment_id: payment_id,
                            payer_id: data.result.payer.payer_info.payer_id
                        })
                    });
                    let json = await response.json();
                    let message = JSON.parse(json)
                    alert(message.state)
                }
                //insert logic here to handle success events or errors, if any
            } catch (exc) {

            }
        }

    </script>
</body>
</html>
