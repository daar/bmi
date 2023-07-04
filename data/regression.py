import pandas as pd
import numpy as np
import matplotlib.pyplot as plt
from sklearn.linear_model import LinearRegression
from sklearn.preprocessing import PolynomialFeatures
from scipy import stats
import pandas as pd

__IMG_PATH__ = "images/"


def load_data_file(input_file):
    """Load data from an Excel file using pandas"""
    return pd.read_csv(input_file)


def train_model(x, y, degree):
    """Train a polynomial regression model using sklearn"""
    poly = PolynomialFeatures(degree=degree)
    x_poly = poly.fit_transform(x)
    model = LinearRegression()
    model.fit(x_poly, y)
    return model


def generate_plots(x, y, model, filename):
    """Generate a parity plot and a residual plot"""
    y_pred = model.predict(
        PolynomialFeatures(degree=model.coef_.shape[1] - 1).fit_transform(x)
    )

    residuals = y - y_pred

    fig, ax = plt.subplots(1, 2, figsize=(12, 6))

    ax[0].plot(y, y_pred, ".", markersize=12)
    ax[0].plot(y, y, "-")
    ax[0].set_xlabel("Measured")
    ax[0].set_ylabel("Predicted")
    ax[0].set_title("Parity Plot")

    ax[1].plot(y_pred, residuals, ".", markersize=12)
    ax[1].axhline(0, color="gray", linestyle="--")
    ax[1].set_xlabel("Predicted")
    ax[1].set_ylabel("Residuals")
    ax[1].set_title("Residual Plot")

    # Save the figures as PNG files
    fig.savefig(__IMG_PATH__ + filename + ".png", dpi=300)

    return fig, residuals


def write_report(file, fig, residuals, model, title, fig_file, x, y):
    """Write a report to a PDF file with regression results and plots"""

    file.write(f"## Regression parameters for {title}\n")

    # Generate coefficient table
    file.write("| C | Values |\n")
    file.write("|----:|-------------:|\n")
    file.write(f"| 0 | {model.intercept_[0]:.15e} |\n")
    for i, coef in enumerate(model.coef_[0][1:]):
        file.write(f"| {i+1} | {coef:.15e} |\n")
    file.write("\n")

    # Regression statistics
    r2 = model.score(
        PolynomialFeatures(degree=model.coef_.shape[1] - 1).fit_transform(x), y
    )
    file.write("- R^2: `{:.3f}`\n".format(r2))
    file.write("- Residual standard error: `{:.3f}`\n".format(np.std(residuals)))
    n = x.shape[0]
    k = model.coef_.shape[1]
    f = (r2 / (k - 1)) / ((1 - r2) / (n - k))
    p_value = 1 - stats.f.cdf(f, k - 1, n - k)
    file.write("- F-statistic: `{:.3f}`\n".format(f))
    file.write("- p-value: `{:.3E}`\n".format(p_value))

    # Include Parity plot and residual plot
    file.write(f"\n![Parity and Residual plot]({__IMG_PATH__ + fig_file}.png)\n")
    file.write("\n")


def perform_regression_analysis(col_name, x, file, title, df, image_name):
    y = df[col_name].values.reshape(-1, 1)

    model = train_model(x, y, degree=4)

    fig, residuals = generate_plots(x, y, model, f"{image_name}{col_name}")

    write_report(
        file, fig, residuals, model, title, f"bmi_girls_0-to-2-years_{col_name}", x, y
    )


def analyse_data_file(data_file, output_file, image_name, title, columns, time_column):
    df = load_data_file(data_file)

    with open(output_file, "w") as file:
        file.write(f"{title}\n\n")

        x = df[time_column].values.reshape(-1, 1)

        for col_name, title_elem in columns:
            perform_regression_analysis(col_name, x, file, title_elem, df, image_name)


if __name__ == "__main__":
    analyse_data_file(
        "./source/bmi_girls_0-to-2-years_zscores.csv",
        "bmi_girls_0-to-2-years.md",
        "bmi_girls_0-to-2-years_",
        "# BMI girls 0 to 2 years of zscores",
        [
            ["SD2neg", "-2 SD"],
            ["SD1", "+1 SD"],
            ["SD2", "+2 SD"],
        ],
        "Month",
    )

    analyse_data_file(
        "./source/bmi_boys_0-to-2-years_zcores.csv",
        "bmi_boys_0-to-2-years.md",
        "bmi_boys_0-to-2-years_",
        "# BMI boys 0 to 2 years of zscores",
        [
            ["SD2neg", "-2 SD"],
            ["SD1", "+1 SD"],
            ["SD2", "+2 SD"],
        ],
        "Month",
    )

    analyse_data_file(
        "./source/bmi_girls_2-to-20-years_zscores.csv",
        "bmi_girls_2-to-20-years.md",
        "bmi_girls_2-to-20-years_",
        "# BMI girls 2 to 20 years of zscores",
        [
            ["-2", "-2 SD"],
            ["1", "+1 SD"],
            ["2", "+2 SD"],
        ],
        "Agemos",
    )

    analyse_data_file(
        "./source/bmi_boys_2-to-20-years_zscores.csv",
        "bmi_boys_2-to-20-years.md",
        "bmi_boys_2-to-20-years_",
        "# BMI boys 2 to 20 years of zscores",
        [
            ["-2", "-2 SD"],
            ["1", "+1 SD"],
            ["2", "+2 SD"],
        ],
        "Agemos",
    )
