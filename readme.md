# WHMCS Woza Payment Gateway

 - **Tags:** woza, whmcs mpesa, payment gateway, whmcs paybill, whmcs till number, mpesa, whmcs
 - **Stable tag:** 1.0.0

Receive M-Pesa payments directly into WHMCS using Woza.

## Description

Accept M-Pesa payment directly to WHMCS using Woza.

#### Take M-Pesa payments easily and directly on WHMCS

Signup for an account [here](https://my.jisort.com/signUp)

Woza is available in:

* __Kenya__

## Installation

### Prerequisites

* Working WHMCS installation (v5.x or above)
* An active application on M-Pesa Daraja portal. Follow the guide [here](https://developer.safaricom.co.ke/docs#step-by-step-go-live-guide)
* Add your Paybill/Till number [here](https://my.jisort.com/mpay/addPaybill/)
* PHP 5.6 or above

### Installation steps

1. Download the [latest release](https://github.com/Jisort/whmcs-mpesa/archive/master.zip) or clone the repository.
2. Extract and copy/upload the `modules` folder to the root of your `<whmcs dir>`.
3. Go to the WHMCS admin area and go to `setup -> payments -> payment gateways`.
4. Click the tab `All Payment Gateways`.
5. Click `Woza` to activate the payment gateway.
6. Select your __Short Code Type__.
7. Enter your __Short Code__.
8. Enter your __Consumer Key__.
9. Enter your __Consumer Secret__.
10. Click __Save Changes__ to save your changes.
