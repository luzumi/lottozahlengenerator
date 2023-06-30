import numpy as np
from dbConnector import get_data
from tensorflow import keras
from tensorflow.keras.layers import Dense

# Abrufen der Daten
numbers = get_data()

# Wir erstellen ein 2D-Array, das für jede Zahl zwei Eigenschaften enthält:
# - Die relative Häufigkeit, mit der sie gezogen wurde.
# - Die Anzahl der Ziehungen seit sie zuletzt gezogen wurde.
features = np.zeros((49, 2))

# Berechnen Sie die relative Häufigkeit jeder Zahl.
counts = np.bincount(numbers.flatten(), minlength=50)
features[:, 0] = counts[1:] / np.sum(counts[1:])

# Berechnen Sie die Anzahl der Ziehungen seit der letzten Ziehung jeder Zahl.
for i in range(1, 50):
    try:
        features[i-1, 1] = np.where(numbers == i)[1].max()
    except ValueError:
        # Wenn eine Zahl noch nie gezogen wurde, setzen wir die Anzahl der Ziehungen auf einen hohen Wert.
        features[i-1, 1] = numbers.shape[1]

# Wir normalisieren die Funktionen, um den Trainingsprozess zu erleichtern.
features = (features - np.mean(features, axis=0)) / np.std(features, axis=0)

# ...
# Zusätzlich zu den einzelnen Zahlen, betrachten wir auch Paare von Zahlen.
pairs = np.zeros((49, 49))

# Wir durchlaufen alle Ziehungen und zählen, wie oft jedes Paar gezogen wurde.
for draw in numbers:
    for i in range(len(draw)):
        for j in range(i+1, len(draw)):
            pairs[draw[i]-1, draw[j]-1] += 1
            pairs[draw[j]-1, draw[i]-1] += 1

# Wir normalisieren die Häufigkeiten, indem wir sie durch die Gesamtzahl der Ziehungen teilen.
pairs /= numbers.shape[0]

# Wir fügen die Paarhäufigkeiten unseren Merkmalen hinzu.
features = np.hstack((features, pairs.reshape((49, -1))))

# ...
# Wir erstellen ein einfaches neuronales Netzwerk, das aus zwei vollständig verbundenen Schichten besteht.
model = keras.Sequential([
    Dense(320, activation='relu', input_shape=(features.shape[1],)),
    Dense(1, activation='sigmoid')
])

model.compile(optimizer='adam', loss='binary_crossentropy')

# Wir erstellen ein Array, das angibt, ob jede Zahl in der nächsten Ziehung auftaucht oder nicht.
targets = np.zeros(49)
targets[numbers[-1] - 1] = 1

# Wir trainieren das Modell, um Vorhersagen auf der Grundlage unserer Funktionen zu treffen.
model.fit(features, targets, epochs=10, verbose=0)

# Um eine Vorhersage zu treffen, wählen wir einfach die 6 Zahlen mit der höchsten Vorhersage aus.
predictions = model.predict(features)
top_numbers = np.argsort(predictions, axis=0)[-6:]

print(top_numbers)