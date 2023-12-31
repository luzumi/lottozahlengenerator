import React, {useEffect, useState} from 'react';
import OptionsMenu from './OptionsMenu'; // korrekter Pfad?
import axios from 'axios'; // library for making HTTP requests



function LastDraw() {
    const [isLastDraw, setIsLastDraw] = useState(true); // Zustand für die Anzeige der letzten Ziehung
    const [date, setDate] = useState(null);
    const [showOptionsMenu, setShowOptionsMenu] = useState(false); // Zustand für das Optionsmenü
    const [calculatedNumbers, setCalculatedNumbers] = useState(null); // Zustand für berechnete Zahlen
    const [csrfToken, setCsrfToken] = useState(null);
    const [isShuffling, setIsShuffling] = useState(false);
    const [flashingNumbers, setFlashingNumbers] = useState(Array(49).fill(false));
    const [resetNumbers, setResetNumbers] = useState(false);
    const [isConfirmed, setIsConfirmed] = useState(false);

    let title;
    if (isShuffling) {
        title = "Ihre Zahlen werden berechnet...";
    } else if (isLastDraw) {
        title = `Letzte Ziehung vom: ${date}`;
    } else {
        title = "Ihre Glückszahlen sind:";
    }


    useEffect(() => {
        let interval;

        if (isShuffling) {
            interval = setInterval(() => {
                const newFlashingNumbers = Array(49).fill(false);
                for (let i = 0; i < 5; i++) { // 5 zufällige Zahlen flackern lassen
                    const randomIndex = Math.floor(Math.random() * 49);
                    newFlashingNumbers[randomIndex] = true;
                }
                setFlashingNumbers(newFlashingNumbers);
            }, 200); // alle 200 ms aktualisieren
        } else {
            setFlashingNumbers(Array(49).fill(false)); // Flackern für alle Zahlen ausschalten
        }

        return () => {
            clearInterval(interval); // Flackern stoppen, wenn die Berechnung abgeschlossen ist
        };
    }, [isShuffling]);

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
        setIsLastDraw(true);
        axios.get('http://localhost:8000/api/last-draw')
            .then(response => {
                const date = response.data.draw_date + "." + response.data.year;
                setDate(date);
                const lottoNumbersObject = response.data.lotto_numbers[0]; // Zugriff auf das lotto_numbers-Array und das erste Objekt darin

                // Extrahieren der Nummern aus dem Objekt und Speichern in einem Array
                const selectedNumbers = [
                    parseInt(lottoNumbersObject.number_one),
                    parseInt(lottoNumbersObject.number_two),
                    parseInt(lottoNumbersObject.number_three),
                    parseInt(lottoNumbersObject.number_four),
                    parseInt(lottoNumbersObject.number_five),
                    parseInt(lottoNumbersObject.number_six)
                ];

                setCalculatedNumbers(selectedNumbers); // Setzen des calculatedNumbers-Zustands mit den ausgewählten Nummern
                console.log("Last draw:", selectedNumbers);
            })
            .catch(error => {
                console.error('Error fetching last draw:', error);
            });
    }


    useEffect(() => {
        console.log("Fetching last draw...");
        fetchLastDraw();
    }, []);


    function confirmSettings() {
        // Schließe das Menü
        toggleOptionsMenu();
        setIsLastDraw(!isLastDraw);
        setIsShuffling(true);
        setCalculatedNumbers(null); // Setze calculatedNumbers auf null, wenn die Berechnung beginnt
        setResetNumbers(true); // Setze die Reset-Flag
        setIsConfirmed(true);

        // Sende die Schiebereglerwerte an die /calculate-Route
        if (csrfToken) {
            axios.post('http://localhost:8000/api/calculate', sliderValues, {
                headers: {
                    'X-CSRF-TOKEN': csrfToken
                }
            }).then(response => {
                const selectedNumbers = response.data;
                setCalculatedNumbers(selectedNumbers); // Speichern Sie die berechneten Zahlen im Zustand
                setIsShuffling(false);
            })
                .catch(error => {
                    console.error('Error sending settings:', error);
                });
        } else {
            console.error('CSRF Token is not available');
        }
    }

    function includesNumber(draw, number) {
        draw.number_six = undefined;
        draw.number_five = undefined;
        draw.number_four = undefined;
        draw.number_three = undefined;
        draw.number_two = undefined;
        draw.number_one = undefined;
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
        if (showOptionsMenu && !isConfirmed) {
            setCalculatedNumbers(null); // Alte Zahlen entfernen, wenn das Menü geschlossen wird und keine Bestätigung erfolgt ist
        }
        setShowOptionsMenu(!showOptionsMenu); // Umschalten des Optionsmenüs
        setIsConfirmed(!isConfirmed); // Zustand der Bestätigung zurücksetzen
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
        <div className={`lotto-container ${isShuffling ? 'pulsating' : ''}`}>
            <div className="toggle-button-container">
                <button className="toggle-button" onClick={toggleOptionsMenu}>
                    <span className="material-icons">tune</span>
                </button>
                <div className='toggle-button-space'></div>
                <button className="toggle-button" onClick={fetchLastDraw}>
                    <span className="material-icons">update</span>
                </button>
            </div>
            <div className="lotto-box">
                <div className="lotto-title-container">
                    <div className={`lotto-title ${isShuffling ? 'blinking' : ''}`}>{title}</div>
                </div>
                <div className="lotto-grid">
                    {[...Array(49)].map((_, i) => {
                        const isDrawn = calculatedNumbers && calculatedNumbers.includes(i + 1);
                        const rotationBefore = isDrawn ? randomRotation(90) : 0;
                        const rotationAfter = isDrawn ? randomRotation(180) : 0;

                        return (
                            <div key={i} className={`lotto-number ${flashingNumbers[i] ? 'flashing' : ''}`}>
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
