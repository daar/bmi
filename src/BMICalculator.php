<?php

/**
 * BMI Calculator Library
 *
 * This library calculates the Body Mass Index (BMI) for a given weight and height and returns the corresponding BMI category
 * according to the World Health Organization (WHO) criteria.
 *
 * References:
 *   WHO Child Growth Standards: https://www.who.int/childgrowth/standards/en/
 *   WHO BMI Classification: https://en.wikipedia.org/wiki/Body_mass_index
 *
 * @author      Darius Blaszyk
 * @license     MIT
 * @version     1.0.0
 */

namespace daar\bmi;

class BMICalculator
{

    public static function calculateBMI(float $weight, float $length)
    {
        $lengthInMeters = $length / 100;
        return round($weight / ($lengthInMeters * $lengthInMeters), 1);
    }

    private static function getBMICategoryForAdults($bmi)
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

    private static function getBMICategoryForChildren($bmi, $ageInMonths, $gender)
    {
        /*
            This function determines the BMI category for children between 0 and 20 years old based on data from the CDC growth charts z-scores. The data has been fitted to a polynomial curve to calculate the BMI for z-scores of -2, 1 and 2 for different genders and ages.

            First, the function calculates the BMI values for the z-scores based on the child's gender and age. Then, it compares the actual BMI with the calculated BMI values to determine the appropriate category. The function returns the determined category.

            https://www.cdc.gov/growthcharts/zscore.htm
            https://www.who.int/toolkits/child-growth-standards/standards/body-mass-index-for-age-bmi-for-age
        */

        if ($gender == 'm') {
            if ($ageInMonths < 24) {
                // boys between 0 and 24 months old
                $table = [
                    [-1.29728259413418e-7, 7.84689046308241e-3, -1.65456674343482e-1, 1.33356031968476, 1.13065193044503e1],
                    [-1.61105344763517e-7, 9.57460469454477e-3, -1.97685070634847e-1, 1.53123355183326, 1.50521721190687e1],
                    [-1.64943751900275e-7, 9.90039750909323e-3, -2.0640797608189e-1, 1.60761269326487, 1.65191452991453e1]
                ];
            } else {
                // boys between 24 months and 20 years old
                $table = [
                    [-5.39706037833735e-9, 2.00456810434705e-6, 2.95724480107785e-5, -0.0339636653732314, 15.1452566765676],
                    [1.05102865036789e-8, -7.84247965949131e-6, 0.00209567535071601, -0.172719125664409, 21.150463505186],
                    [4.51062198494927e-8, -2.73893124982513e-5, 0.00567658994707569, -0.378841681547425, 26.3336384248355]
                ];
            }
        } else {
            if ($ageInMonths < 24) {
                // girls between 0 and 24 months old
                $table = [
                    [-1.02477898579848e-4, 6.32170705633978e-3, -1.36237819318779e-1, 1.12242354153149, 1.11382080754494e1],
                    [-1.47097197322086e-4, 8.88971403963913e-3, -1.86495315861883e-1, 1.46835688410652, 1.47712407898615e1],
                    [-1.57511570555050e-4, 9.54511671902983e-3, -2.01035184622142e-1, 1.58848518631128, 1.62464957264957e1]
                ];
            } else {
                // girls between 24 months and 20 years old
                $table = [
                    [-8.06889683739523e-9, 2.93060368998674e-6, -7.53299400479169e-5, -0.0286919110868747, 14.7237641662902],
                    [1.36656323401649e-8, -1.01585725019333e-5, 0.00255710802017196, -0.197162867146647, 21.3636679182733],
                    [3.14161451810361e-8, -1.94485849827012e-5, 0.00418777423789151, -0.263661726303526, 23.9406254419527]
                ];
            }
        }

        // calculate BMI for each z-score from growthcharts
        $bmiGrowthChart = [0, 0, 0];
        for ($rowIndex = 0; $rowIndex < 3; $rowIndex++) {

            // Retrieve the constants for the selected z-score
            $constants = $table[$rowIndex];

            // Compute the BMI growth chart using a 4th order polynomial equation
            for ($i = 0; $i < 5; $i++) {
                $bmiGrowthChart[$rowIndex] += $constants[$i] * pow($ageInMonths, 4 - $i);
            }
        }

        if ($bmi < $bmiGrowthChart[0]) {
            $category = 'Underweight'; // z-score < -2
        } elseif ($bmi < $bmiGrowthChart[1]) {
            $category = 'Normal weight'; // -2 <= z-score < 1
        } elseif ($bmi < $bmiGrowthChart[2]) {
            $category = 'Overweight'; // 1 <= z-score < 2
        } else {
            $category = 'Obese'; // z-score >= 2
        }

        return $category;
    }

    public static function calculateBMICategory(float $weight, float $length, string $gender, float $ageInMonths)
    {
        $bmi = self::calculateBMI($weight, $length);

        if ($ageInMonths < 24) {
            return self::getBMICategoryForChildren($bmi, $ageInMonths, $gender);
        } else {
            return  self::getBMICategoryForAdults($bmi);
        }
    }
}
