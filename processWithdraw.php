<?php
    require_once('connect-db.php');
    session_start();
    $errors = array();
    $data  = array();


    if(empty($_POST['amount-to-withdraw'])){
        $errors['deposit-amount'] = "Amount to Withdraw is required";
    }
    if(!empty($errors)){
        $data[0]= false;
        $data[1]= $errors;

    }
    else{
        //If there are no errors process deposit
        $withdrawAmount = $_POST['amount-to-withdraw'];
        if($withdrawAmount > $_SESSION['wallet-balance']){
            echo"<script>
                var withdrawModal = document.querySelector('#modal-withdraw');
                 var instance = M.Modal.init(withdrawModal);
                instance.close();
                $('#w-balance').load('updateWallet.php');
                M.toast({html:'You have insuffieicent funds', classes:'rounded red', displayLength:'1000'});
            </script>";
            $data[2] = $msg;
        }
        else{
            $total_amount= $_SESSION['wallet-balance'] - $withdrawAmount;
            $withdrawMoneySQL = "UPDATE wallet SET WalletBalance='$total_amount' WHERE User_ID='{$_SESSION['idNo']}'";
            $withdrawWallet= $con->query($withdrawMoneySQL);
            $transactionType ="Cash Withdrawal";
            $insertTransactionsSQL = "INSERT INTO transactions (User_ID,Amount,TransactionType) VALUES('{$_SESSION['idNo']}','$withdrawAmount','$transactionType')";
            $insertTransactions = $con->query($insertTransactionsSQL);
            $data[3] = true;
            $data[4] = "Successfully Withdrawn".$_POST['amount-to-withdraw'] ;
        }
       

    }
    $dataJSON = json_encode($data);
    echo $dataJSON;
?>