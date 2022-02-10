<?php

namespace GloCurrency\FidelityBank\Enums;

use GloCurrency\MiddlewareBlocks\Enums\ProcessingItemStateCodeEnum as MProcessingItemStateCodeEnum;
use BrokeYourBike\FidelityBank\Enums\StatusCodeEnum;
use BrokeYourBike\FidelityBank\Enums\ErrorCodeEnum;

enum TransactionStateCodeEnum: string
{
    case LOCAL_UNPROCESSED = 'local_unprocessed';
    case LOCAL_EXCEPTION = 'local_exception';
    case STATE_NOT_ALLOWED = 'state_not_allowed';
    case API_REQUEST_EXCEPTION = 'api_request_exception';
    case RESULT_JSON_INVALID = 'result_json_invalid';
    case NO_ERROR_CODE_PROPERTY = 'no_error_code_property';
    case UNEXPECTED_ERROR_CODE = 'unexpected_error_code';
    case UNEXPECTED_STATUS_CODE = 'unexpected_transaction_status';
    case PROCESSING = 'processing';
    case PAID = 'paid';
    case FAILED = 'failed';
    case CANCELED = 'canceled';
    case API_ERROR = 'api_error';
    case INSUFFICIENT_FUNDS = 'insufficient_funds';
    case DUPLICATE_TRANSACTION = 'duplicate_transaction';
    case RECIPIENT_BANK_ACCOUNT_INVALID = 'recipient_bank_account_invalid';
    case RECIPIENT_BANK_CODE_INVALID = 'recipient_bank_code_invalid';
    case RECIPIENT_NAME_VALIDATION_FAILED = 'recipient_name_validation_failed';
    case RECIPIENT_TRANSFER_LIMIT_EXCEEDED = 'recipient_transfer_limit_exceeded';
    case RECIPIENT_DETAILS_INVALID = 'recipient_details_invalid';

    public static function makeFromErrorCode(ErrorCodeEnum $errorCode): TransactionStateCodeEnum
    {
        return match ($errorCode) {
            ErrorCodeEnum::PAID => self::PAID,
            ErrorCodeEnum::INVALID_ACCOUNT => self::RECIPIENT_BANK_ACCOUNT_INVALID,
            ErrorCodeEnum::NOT_PERMITTED => self::API_ERROR,
            ErrorCodeEnum::TRANSACTION_LIMIT => self::RECIPIENT_TRANSFER_LIMIT_EXCEEDED,
            ErrorCodeEnum::INSUFFICIENT_FUNDS => self::INSUFFICIENT_FUNDS,
            ErrorCodeEnum::DUPLICATE_TRANSACTION => self::DUPLICATE_TRANSACTION,
            ErrorCodeEnum::INVALID_RECIPIENT => self::RECIPIENT_DETAILS_INVALID,
            ErrorCodeEnum::AUTH_FAILED => self::API_ERROR,
            ErrorCodeEnum::SYSTEM_EXCEPTION => self::API_ERROR,
            ErrorCodeEnum::SYSTEM_MALFUNCTION => self::API_ERROR,
            ErrorCodeEnum::IN_PROGRESS => self::PROCESSING,
            ErrorCodeEnum::NAME_MISMATCH => self::RECIPIENT_NAME_VALIDATION_FAILED,
            ErrorCodeEnum::INVALID_PIN => self::API_ERROR,
            ErrorCodeEnum::INVALID_BANK_CODE => self::RECIPIENT_BANK_CODE_INVALID,
            ErrorCodeEnum::INVALID_BANK => self::RECIPIENT_BANK_CODE_INVALID,
            ErrorCodeEnum::ACCOUNT_NOT_FOUND => self::RECIPIENT_BANK_ACCOUNT_INVALID,
            ErrorCodeEnum::INVALID_ACCOUNT_STATUS => self::RECIPIENT_BANK_ACCOUNT_INVALID,
        };
    }

    public static function makeFromStatusCode(StatusCodeEnum $statusCode): TransactionStateCodeEnum
    {
        return match ($statusCode) {
            StatusCodeEnum::TRANSMIT => self::PROCESSING,
            StatusCodeEnum::IN_PROGRESS => self::PROCESSING,
            StatusCodeEnum::PAID => self::PAID,
            StatusCodeEnum::CANCELED => self::CANCELED,
            StatusCodeEnum::ERROR => self::FAILED,
        };
    }

    /**
     * Get the ProcessingItem state based on Transaction state.
     */
    public function getProcessingItemStateCode(): MProcessingItemStateCodeEnum
    {
        return match ($this) {
            self::LOCAL_UNPROCESSED => MProcessingItemStateCodeEnum::PENDING,
            self::LOCAL_EXCEPTION => MProcessingItemStateCodeEnum::MANUAL_RECONCILIATION_REQUIRED,
            self::STATE_NOT_ALLOWED => MProcessingItemStateCodeEnum::EXCEPTION,
            self::API_REQUEST_EXCEPTION => MProcessingItemStateCodeEnum::EXCEPTION,
            self::RESULT_JSON_INVALID => MProcessingItemStateCodeEnum::EXCEPTION,
            self::NO_ERROR_CODE_PROPERTY => MProcessingItemStateCodeEnum::EXCEPTION,
            self::UNEXPECTED_ERROR_CODE => MProcessingItemStateCodeEnum::EXCEPTION,
            self::UNEXPECTED_STATUS_CODE => MProcessingItemStateCodeEnum::EXCEPTION,
            self::PROCESSING => MProcessingItemStateCodeEnum::PROVIDER_PENDING,
            self::PAID => MProcessingItemStateCodeEnum::PROCESSED,
            self::FAILED => MProcessingItemStateCodeEnum::MANUAL_RECONCILIATION_REQUIRED,
            self::CANCELED => MProcessingItemStateCodeEnum::TRANSACTION_CANCELED_BY_PROVIDER,
            self::API_ERROR => MProcessingItemStateCodeEnum::PROVIDER_NOT_ACCEPTING_TRANSACTIONS,
            self::INSUFFICIENT_FUNDS => MProcessingItemStateCodeEnum::PROVIDER_NOT_ACCEPTING_TRANSACTIONS,
            self::DUPLICATE_TRANSACTION => MProcessingItemStateCodeEnum::EXCEPTION,
            self::RECIPIENT_BANK_ACCOUNT_INVALID => MProcessingItemStateCodeEnum::RECIPIENT_BANK_ACCOUNT_INVALID,
            self::RECIPIENT_BANK_CODE_INVALID => MProcessingItemStateCodeEnum::RECIPIENT_BANK_CODE_INVALID,
            self::RECIPIENT_NAME_VALIDATION_FAILED => MProcessingItemStateCodeEnum::RECIPIENT_NAME_VALIDATION_FAILED,
            self::RECIPIENT_TRANSFER_LIMIT_EXCEEDED => MProcessingItemStateCodeEnum::RECIPIENT_TRANSFER_LIMIT_EXCEEDED,
            self::RECIPIENT_DETAILS_INVALID => MProcessingItemStateCodeEnum::RECIPIENT_DETAILS_INVALID,
        };
    }
}
