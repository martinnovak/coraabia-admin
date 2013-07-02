<?php

namespace Model\Transaction;



interface ITransactionFactory
{
    /** @return \Model\Transaction\Transaction */
    function create();
}
