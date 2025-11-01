<?php

/**
 * Validator Utility
 * Input validation helper functions
 */

class Validator
{

    /**
     * Validate required fields
     */
    public static function required($data, $fields)
    {
        $errors = [];

        foreach ($fields as $field) {
            if (!isset($data[$field]) || empty(trim($data[$field]))) {
                $errors[$field] = ucfirst(str_replace('_', ' ', $field)) . ' is required';
            }
        }

        return $errors;
    }

    /**
     * Validate email format
     */
    public static function email($email)
    {
        return filter_var($email, FILTER_VALIDATE_EMAIL) !== false;
    }

    /**
     * Validate minimum length
     */
    public static function minLength($value, $min)
    {
        return strlen($value) >= $min;
    }

    /**
     * Validate maximum length
     */
    public static function maxLength($value, $max)
    {
        return strlen($value) <= $max;
    }

    /**
     * Validate numeric value
     */
    public static function numeric($value)
    {
        return is_numeric($value);
    }

    /**
     * Validate integer value
     */
    public static function integer($value)
    {
        return filter_var($value, FILTER_VALIDATE_INT) !== false;
    }

    /**
     * Validate positive number
     */
    public static function positive($value)
    {
        return is_numeric($value) && $value > 0;
    }

    /**
     * Validate date format
     */
    public static function date($date, $format = 'Y-m-d')
    {
        $d = DateTime::createFromFormat($format, $date);
        return $d && $d->format($format) === $date;
    }

    /**
     * Validate phone number (Philippine format)
     */
    public static function phone($phone)
    {
        // Allow formats: +639171234567, 09171234567, 9171234567
        $pattern = '/^(\+639|09|9)\d{9}$/';
        return preg_match($pattern, $phone);
    }

    /**
     * Validate enum value
     */
    public static function enum($value, $allowedValues)
    {
        return in_array($value, $allowedValues);
    }

    /**
     * Sanitize string input
     */
    public static function sanitizeString($value)
    {
        return htmlspecialchars(strip_tags(trim($value)), ENT_QUOTES, 'UTF-8');
    }

    /**
     * Sanitize email input
     */
    public static function sanitizeEmail($email)
    {
        return filter_var($email, FILTER_SANITIZE_EMAIL);
    }

    /**
     * Validate and sanitize integer
     */
    public static function sanitizeInt($value)
    {
        return filter_var($value, FILTER_SANITIZE_NUMBER_INT);
    }

    /**
     * Validate password strength
     * Must be at least 8 characters with uppercase, lowercase, number, and special char
     */
    public static function strongPassword($password)
    {
        $errors = [];

        if (strlen($password) < 8) {
            $errors[] = 'Password must be at least 8 characters long';
        }
        if (!preg_match('/[A-Z]/', $password)) {
            $errors[] = 'Password must contain at least one uppercase letter';
        }
        if (!preg_match('/[a-z]/', $password)) {
            $errors[] = 'Password must contain at least one lowercase letter';
        }
        if (!preg_match('/[0-9]/', $password)) {
            $errors[] = 'Password must contain at least one number';
        }
        if (!preg_match('/[@$!%*?&#]/', $password)) {
            $errors[] = 'Password must contain at least one special character (@$!%*?&#)';
        }

        return $errors;
    }
}
