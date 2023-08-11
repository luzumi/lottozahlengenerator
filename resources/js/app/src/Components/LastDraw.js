import React, {useEffect, useState} from 'react';
import OptionsMenu from './OptionsMenu'; // korrekter Pfad?
import axios from 'axios'; // library for making HTTP requests

function LastDraw() {
    const [lastDraw, setLastDraw] = useState(null);
    const [date, setDate] = useState(null);
    const [showOptionsMenu, setShowOptionsMenu] = useState(false); // Zustand für das Optionsmenü
    const [calculatedNumbers, setCalculatedNumbers] = useState(null); // Zustand für berechnete Zahlen
    const [csrfToken, setCsrfToken] = useState(null);


    useEffect(() => {
        axios.get('http://localhost:8000/csrf-token').then(response => {
            const token = response.data.csrfToken;
            console.log("CSRF Token from server:", token);
            setCsrfToken(token);
        });
    }, []);

    const [sliderValues, setSliderValues] = useState({
        frequent: 50,
        rare: 50,
        longestAbsence: 50,
        frequentPairs: 50
    });

    function handleSliderChange(name, value) {
        setSliderValues({
            ...sliderValues,
            [name]: value
        });
    }


    function fetchLastDraw() {
        axios.get('http://localhost:8000/api/last-draw')
            .then(response => {
                const date = response.data.draw_date + "." + response.data.year;
                setDate(date);
                setLastDraw(response.data);
            })
            .catch(error => {
                console.error('Error fetching last draw:', error);
            });
    }

    // Verwenden der neuen Funktion in einem useEffect Hook
    useEffect(() => {
        fetchLastDraw();

    }, []);

    function confirmSettings() {
        // Schließe das Menü
        toggleOptionsMenu();

        // Sende die Schiebereglerwerte an die /calculate-Route
        if (csrfToken) {
            axios.post('http://localhost:8000/api/calculate', sliderValues, {
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                }
            }).then(response => {
                const selectedNumbers = response.data;
                setCalculatedNumbers(selectedNumbers); // Speichern Sie die berechneten Zahlen im Zustand

            })
                .catch(error => {
                    console.error('Error sending settings:', error);
                });
        } else {
            console.error('CSRF Token is not available');
        }
    }


    useEffect(() => {
        // make a request to your Laravel server to get the last draw
        axios.get('http://localhost:8000/api/last-draw')
            .then(response => {
                const date = response.data.draw_date + "." + response.data.year;
                setDate(date);
                // console.log(response.data.draw_date + "." + response.data.year);
                setLastDraw(response.data); // Set lastDraw to the entire response
            })
            .catch(error => {
                console.error('Error fetching last draw:', error);
            });

    }, []);

    function includesNumber(draw, number) {
        const drawnNumbers = [
            draw.number_one,
            draw.number_two,
            draw.number_three,
            draw.number_four,
            draw.number_five,
            draw.number_six
        ].map(Number);

        return drawnNumbers.includes(number);
    }

    function toggleOptionsMenu() {
        setShowOptionsMenu(!showOptionsMenu); // Umschalten des Optionsmenüs
    }

    function randomRotation(degree) {
        return Math.floor(Math.random() * 30) - degree; // Random number between -10 and 10
    }

    function randomCurve() {
        return `M10 10 Q${Math.random() * 70 + 10} ${Math.random() * 40 + 30} 90 10`;
    }

    function updateScale() {
        // Maximale Höhe für den Container
        const maxHeight = 400;
        const maxWidth = 600;

        // Aktuelle Bildschirmhöhe
        const screenHeight = window.innerHeight;
        const screenWidth = window.innerWidth;

        // Berechnen Sie den Skalierungsfaktor basierend auf der Bildschirmhöhe
        let scaleFactor = screenHeight / maxHeight;
        let scaleFactorWidth = screenWidth / maxWidth;

        // Begrenzen Sie den Skalierungsfaktor auf 1, um eine Übervergrößerung zu vermeiden
        if (scaleFactor > 1) {
            scaleFactor = 1;
        }

        // Aktualisieren Sie die CSS-Variable mit dem neuen Skalierungsfaktor
        document.documentElement.style.setProperty('--scale-factor', scaleFactor);
        document.documentElement.style.setProperty('--scale-factor-width', scaleFactorWidth);
    }

// Aktualisieren Sie die Skalierung beim Laden der Seite
    updateScale();

// Aktualisieren Sie die Skalierung, wenn die Fenstergröße geändert wird
    window.addEventListener('resize', updateScale);

    return (
        <div className="lotto-container">
            <button className="toggle-button" onClick={toggleOptionsMenu}>
                <span className="material-icons">tune</span>
            </button>
            <div className="lotto-box">
                <div className="lotto-title-container">
                    <div className="lotto-title">Letzte Ziehung vom:</div>
                    <div className="lotto-title">{date}</div>
                </div>
                <div className="lotto-grid">
                    {[...Array(49)].map((_, i) => {
                        const isDrawn = calculatedNumbers
                            ? calculatedNumbers.includes(i + 1)
                            : lastDraw && includesNumber(lastDraw.lotto_numbers[0], i + 1);

                        const rotationBefore = isDrawn ? randomRotation(90) : 0;
                        const rotationAfter = isDrawn ? randomRotation(180) : 0;

                        return (
                            <div key={i} className={`lotto-number ${isDrawn ? 'drawn' : ''}`}>
                                {i + 1}
                                {isDrawn && (
                                    <>
                                        <svg className="x-line" style={{transform: `rotate(${rotationBefore}deg)`}}>
                                            <path d={randomCurve()} stroke="currentColor" fill="none" strokeWidth="4"/>
                                        </svg>
                                        <svg className="x-line" style={{transform: `rotate(${rotationAfter}deg)`}}>
                                            <path d={randomCurve()} stroke="currentColor" fill="none" strokeWidth="4"/>
                                        </svg>
                                    </>
                                )}
                            </div>
                        );
                    })}
                </div>
            </div>
            {showOptionsMenu && (
                <OptionsMenu
                    sliders={sliderValues} // Übergabe der Schiebereglerwerte
                    toggle={toggleOptionsMenu}
                    onSliderChange={handleSliderChange}
                    onConfirm={confirmSettings}
                />
            )}

        </div>

    );
}

export default LastDraw;
