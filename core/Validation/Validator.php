<?php
namespace Core\Validation;

class Validator
{
    protected $errors = [];

    /**
     * Validate the given data against the given rules.
     *
     * @param array $data The data to be validated
     * @param array $rules The rules to validate against. Each key is the field name and the value is the rule or rules delimited by a pipe.
     *
     * Example:
     * 'name' => 'required|max:255',
     * 'email' => 'required|email',
     *
     * @return bool True if the validation passes, false otherwise.
     */
    public function validate(array $data, array $rules): bool
    {
        $this->errors = [];

        foreach ($rules as $field => $ruleString) {
            $rulesArr = explode('|', $ruleString);

            foreach ($rulesArr as $rule) {
                if ($rule === 'required' && empty($data[$field])) {
                    $this->errors[$field][] = 'The ' . $field . ' field is required.';
                }
                if (strpos($rule, 'max:') === 0) {
                    $max = (int)substr($rule, 4);
                    if (strlen($data[$field] ?? '') > $max) {
                        $this->errors[$field][] = "The $field may not be greater than $max characters.";
                    }
                }
                if (strpos($rule, 'email') === 0) {
                    if (!filter_var($data[$field] ?? '', FILTER_VALIDATE_EMAIL)) {
                        $this->errors[$field][] = "The $field must be a valid email address.";
                    }
                }
            }
        }
        return empty($this->errors);
    }

    /**
     * Get the validation errors
     *
     * @return array
     */
    public function errors(): array
    {
        return $this->errors;
    }
}