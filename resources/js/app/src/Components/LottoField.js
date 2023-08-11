import React, { useState, useEffect } from 'react';
import './LottoField.css'; // Importieren Sie Ihre CSS-Datei

const LottoField = () => {
    const [lottoNumbers, setLottoNumbers] = useState(null);
    const [shuffling, setShuffling] = useState(false);

    // Simuliert das Abrufen der berechneten Zahlen vom Server
    const fetchLottoNumbers = () => {
        setShuffling(true);
        setTimeout(() => {
            setLottoNumbers([1, 2, 3, 4, 5, 6]);
            setShuffling(false);
        }, 6000); // 6-Sekunden-Verzögerung
    };

    useEffect(() => {
        // Rufen Sie die Zahlen ab, wenn die Komponente zum ersten Mal geladen wird
        fetchLottoNumbers();
    }, []);

    return (
        <div className="lotto-field">
            {shuffling
                ? <div className="shuffling">Shuffling...</div> // Shuffle-Animation
                : lottoNumbers && lottoNumbers.map((number) => (
                <div key={number} className="lotto-number">
                    {number}
                </div>
            ))}
            <button onClick={fetchLottoNumbers}>Neue Zahlen</button>
        </div>
    );
};

export default LottoField;
