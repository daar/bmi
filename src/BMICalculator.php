<?php

/**
 * BMI Calculator Library
 *
 * This library calculates the Body Mass Index (BMI) for a given weight and height and returns the corresponding BMI category
 * according to the World Health Organization (WHO) criteria.
 *
 * References:
 * WHO Child Growth Standards: https://www.who.int/childgrowth/standards/en/
 * WHO BMI Classification: https://www.who.int/health-topics/obesity#tab=tab_1
 *
 * @author      Darius Blaszyk
 * @license     MIT
 * @version     1.0.0
 */

namespace daar\bmi;

class BMICalculator
{

    private $weight;
    private $length;
    private $gender;
    private $age;
    private $bmi;

    public function __construct(float $weight, float $length, string $gender, float $age)
    {
        $this->weight = $weight;
        $this->length = $length;
        $this->gender = $gender;
        $this->age = $age;

        $this->bmi = round($weight / ($length * $length), 1);
    }

    public function calculateBMI(float $weight, float $length)
    {
        return $this->bmi;
    }

    private function getBMICategoryForAdults($bmi)
    {
        if ($bmi < 16) {
            return 'Underweight (Severe thinness)';
        } elseif ($bmi <= 16.9) {
            return 'Underweight (Moderate thinness)';
        } elseif ($bmi <= 18.4) {
            return 'Underweight (Mild thinness)';
        } elseif ($bmi <= 24.9) {
            return 'Normal weight';
        } elseif ($bmi <= 29.9) {
            return 'Overweight (Pre-obese)';
        } elseif ($bmi <= 34.9) {
            return 'Obese class I';
        } elseif ($bmi <= 39.9) {
            return 'Obese class II';
        } else {
            return 'Obese class III';
        }
    }

    public function BMICategory()
    {
        if ($this->age >= 20) {
            return  $this->getBMICategoryForAdults($this->bmi);
        } else {
            // return $this->getBMICategoryForChildren($bmi, $age, $gender);
        }
    }
}
