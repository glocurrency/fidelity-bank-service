<?php

namespace GloCurrency\FidelityBank\Enums;

use BrokeYourBike\FidelityBank\Enums\ErrorCodeEnum;

class ErrorCodeFactory
{
    public static function getTransactionStateCode(ErrorCodeEnum $errorCode): TransactionStateCodeEnum
    {
        return match ($errorCode) {
            ErrorCodeEnum::PAID => TransactionStateCodeEnum::PAID,
            ErrorCodeEnum::INVALID_ACCOUNT => TransactionStateCodeEnum::RECIPIENT_BANK_ACCOUNT_INVALID,
            ErrorCodeEnum::NOT_PERMITTED => TransactionStateCodeEnum::API_ERROR,
            ErrorCodeEnum::TRANSACTION_LIMIT => TransactionStateCodeEnum::RECIPIENT_TRANSFER_LIMIT_EXCEEDED,
            ErrorCodeEnum::INSUFFICIENT_FUNDS => TransactionStateCodeEnum::INSUFFICIENT_FUNDS,
            ErrorCodeEnum::DUPLICATE_TRANSACTION => TransactionStateCodeEnum::DUPLICATE_TRANSACTION,
            ErrorCodeEnum::INVALID_RECIPIENT => TransactionStateCodeEnum::RECIPIENT_DETAILS_INVALID,
            ErrorCodeEnum::AUTH_FAILED => TransactionStateCodeEnum::API_ERROR,
            ErrorCodeEnum::SYSTEM_EXCEPTION => TransactionStateCodeEnum::API_ERROR,
            ErrorCodeEnum::SYSTEM_MALFUNCTION => TransactionStateCodeEnum::API_ERROR,
            ErrorCodeEnum::IN_PROGRESS => TransactionStateCodeEnum::PROCESSING,
            ErrorCodeEnum::NAME_MISMATCH => TransactionStateCodeEnum::RECIPIENT_NAME_VALIDATION_FAILED,
            ErrorCodeEnum::INVALID_PIN => TransactionStateCodeEnum::API_ERROR,
            ErrorCodeEnum::INVALID_BANK_CODE => TransactionStateCodeEnum::RECIPIENT_BANK_CODE_INVALID,
            ErrorCodeEnum::INVALID_BANK => TransactionStateCodeEnum::RECIPIENT_BANK_CODE_INVALID,
            ErrorCodeEnum::ACCOUNT_NOT_FOUND => TransactionStateCodeEnum::RECIPIENT_BANK_ACCOUNT_INVALID,
            ErrorCodeEnum::INVALID_ACCOUNT_STATUS => TransactionStateCodeEnum::RECIPIENT_BANK_ACCOUNT_INVALID,
        };
    }
}
