from flask import Flask, jsonify
import pandas as pd
from sklearn.tree import DecisionTreeRegressor
from sklearn.model_selection import train_test_split
from flask_cors import CORS  # Importa CORS

app = Flask(__name__)

# Habilita CORS para todas las rutas
CORS(app)

@app.route('/predecir_mas_vendidos', methods=['GET'])
def predecir_mas_vendidos():
    try:
        # Cargar el dataset desde la ruta especificada
        df = pd.read_csv(r'C:\xampp\htdocs\minasmarket\dataset_tendencia_ventas_supermercado.csv', encoding='ISO-8859-1', delimiter=';')

        # Convertir la columna 'Fecha' a formato datetime
        df['Fecha'] = pd.to_datetime(df['Fecha'], format='%d/%m/%Y')

        # Extraer el mes y el año de la fecha para la predicción por mes
        df['Mes'] = df['Fecha'].dt.month
        df['Año'] = df['Fecha'].dt.year

        # Agrupar por Producto, Mes y Año para obtener las ventas totales
        df_grouped = df.groupby(['Año', 'Mes', 'ID_Producto', 'NombreProducto'], as_index=False).agg({'CantidadVendida': 'sum'})

        # Definir las características (X) y la etiqueta (y)
        X = df_grouped[['Año', 'Mes']]  # Año y Mes como características
        y = df_grouped['CantidadVendida']  # Cantidad vendida como objetivo

        # Dividir en conjunto de entrenamiento y prueba
        X_train, X_test, y_train, y_test = train_test_split(X, y, test_size=0.2, random_state=42)

        # Usar un modelo de regresión (Árbol de Decisión para regresión)
        model = DecisionTreeRegressor()
        model.fit(X_train, y_train)

        # Realizar predicciones para todos los productos y meses
        df_grouped['prediccion_ventas'] = model.predict(df_grouped[['Año', 'Mes']])

        # Redondear las predicciones a unidades enteras
        df_grouped['prediccion_ventas'] = df_grouped['prediccion_ventas'].round()

        # Ordenar los productos por predicción de ventas (de mayor a menor)
        df_sorted = df_grouped.sort_values(by='prediccion_ventas', ascending=False)

        # Seleccionar los productos más vendidos para cada mes
        productos_mas_vendidos = df_sorted[['Año', 'Mes', 'NombreProducto', 'prediccion_ventas']]

        # Convertir los resultados a formato JSON
        resultados = productos_mas_vendidos.to_dict(orient='records')

        # Devolver los resultados como JSON
        return jsonify(resultados)

    except Exception as e:
        # Captura cualquier error y lo devuelve como JSON con el mensaje de error
        return jsonify({"error": f"Se ha producido un error: {str(e)}"}), 500  # Código de error 500 para errores del servidor

# Asegúrate de que el servidor Flask se ejecute en el puerto 5000
if __name__ == '__main__':
    app.run(debug=True, port=5000)
