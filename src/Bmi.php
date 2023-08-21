<?php

/**
 * This library calculates the Body Mass Index (BMI) for a given weight and height and returns the corresponding BMI category
 * according to the World Health Organization (WHO) criteria.
 *
 * References:
 *   WHO Child Growth Standards: https://www.who.int/childgrowth/standards/en/
 *   WHO BMI Classification: https://en.wikipedia.org/wiki/Body_mass_index
 */

namespace Daar\Bmi;

class Bmi
{
    public static function calculate(float $weight, float $length)
    {
        $lengthInMeters = $length / 100;
        return round($weight / ($lengthInMeters * $lengthInMeters), 1);
    }

    private static function getBMICategoryForAdults($bmi)
    {
        if ($bmi < 16) {
            return __('bmi::translations.adults.severe-underweight');
        } elseif ($bmi <= 16.9) {
            return __('bmi::translations.adults.moderate-underweight');
        } elseif ($bmi <= 18.4) {
            return __('bmi::translations.adults.mild-underweight');
        } elseif ($bmi <= 24.9) {
            return __('bmi::translations.adults.normal');
        } elseif ($bmi <= 29.9) {
            return __('bmi::translations.adults.overweight');
        } elseif ($bmi <= 34.9) {
            return __('bmi::translations.adults.obese-class-1');
        } elseif ($bmi <= 39.9) {
            return __('bmi::translations.adults.obese-class-2');
        } else {
            return __('bmi::translations.adults.obese-class-3');
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
            //boys
            $table = ($ageInMonths < 24) ? [
                [1.130651930444735e+01, 1.333560319685908e+00, -1.654566743439486e-01, 7.846890463119233e-03, -1.297282594141524e-04],
                [1.505217211907924e+01, 1.531233551834507e+00, -1.976850706353717e-01, 9.574604694586403e-03, -1.611053447645450e-04],
                [1.651914529915286e+01, 1.607612693266383e+00, -2.064079760824746e-01, 9.900397509138917e-03, -1.649437519013475e-04],
            ] : [
                [1.514525667582838e+01, -3.396366538181846e-02, 2.957244859807064e-05, 2.004568100939097e-06, -5.397060375000873e-09],
                [2.115046352674299e+01, -1.727191258040158e-01, 2.095675355004263e-03, -7.842479683740629e-06, 1.051028650222996e-08],
                [2.633363841221892e+01, -3.788416818483565e-01, 5.676589956409809e-03, -2.738931255105844e-05, 4.510621992581676e-08],
            ];
        } else {
            // girls
            $table = ($ageInMonths < 24) ? [
                [1.113820807547422e+01, 1.122423541533045e+00, -1.362378193192714e-01, 6.321707056376615e-03, -1.024778985809771e-04],
                [1.477124078987601e+01, 1.468356884108020e+00, -1.864953158624385e-01, 8.889714039682084e-03, -1.470971973231983e-04],
                [1.624649572649810e+01, 1.588485186313076e+00, -2.010351846227709e-01, 9.545116719077998e-03, -1.575115705561014e-04],
            ] : [
                [1.472376416842509e+01, -2.869191111181625e-02, -7.532993931178362e-05, 2.930603685831956e-06, -8.068896835411454e-09],
                [2.136366791524148e+01, -1.971628673066984e-01, 2.557108025076406e-03, -1.015857252966421e-05, 1.366563237570340e-08],
                [2.394062542599380e+01, -2.636617264947195e-01, 4.187774244140183e-03, -1.944858501813683e-05, 3.141614524082570e-08],
            ];
        }

        // calculate BMI for each z-score from growthcharts
        $bmiGrowthChart = [0, 0, 0];
        for ($rowIndex = 0; $rowIndex < 3; $rowIndex++) {

            // Retrieve the constants for the selected z-score
            $constants = $table[$rowIndex];

            // Compute the BMI growth chart using a 4th order polynomial equation
            for ($i = 0; $i < 5; $i++) {
                $bmiGrowthChart[$rowIndex] += $constants[$i] * pow($ageInMonths, $i);
            }
        }

        if ($bmi < $bmiGrowthChart[0]) {
            $category = trans('bmi::translations.children.underweight'); // z-score < -2
        } elseif ($bmi < $bmiGrowthChart[1]) {
            $category = __('bmi::translations.children.normal'); // -2 <= z-score < 1
        } elseif ($bmi < $bmiGrowthChart[2]) {
            $category = __('bmi::translations.children.overweight'); // 1 <= z-score < 2
        } else {
            $category = __('bmi::translations.children.obese'); // z-score >= 2
        }

        return $category;
    }

    public static function category(float $weight, float $length, string $gender, float $ageInMonths)
    {
        $bmi = self::calculate($weight, $length);

        // up to 20 years use different categories
        if ($ageInMonths < 240) {
            return self::getBMICategoryForChildren($bmi, $ageInMonths, $gender);
        } else {
            return self::getBMICategoryForAdults($bmi);
        }
    }
}