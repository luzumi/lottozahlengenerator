from dotenv import load_dotenv
import os
import mysql.connector
import numpy as np

# Laden Sie die Umgebungsvariablen
load_dotenv()


def get_data():
    # Erstellen Sie eine Verbindung zur Datenbank
    db = mysql.connector.connect(
        host=os.getenv("DB_HOST"),
        user=os.getenv("DB_USERNAME"),
        password=os.getenv("DB_PASSWORD"),
        database=os.getenv("DB_DATABASE")
    )

    cursor = db.cursor()

    # Führen Sie eine SQL-Abfrage aus
    cursor.execute("SELECT * FROM lotto_numbers")

    # Holen Sie alle Zeilen aus der Abfrage
    rows = cursor.fetchall()

    # Konvertieren Sie die Liste der Tupel in ein Numpy-Array
    # Extrahieren Sie nur die sechs Lottozahlen aus jeder Zeile und konvertieren Sie sie in ein Numpy-Array
    numbers = np.array([row[2:8] for row in rows], dtype=int)


    # Vergessen Sie nicht, die Verbindung zu schließen, wenn Sie fertig sind
    db.close()

    return numbers

get_data()
