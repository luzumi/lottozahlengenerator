import React, {useEffect, useState} from 'react';
import axios from 'axios'; // library for making HTTP requests

function LastDraw() {
    const [lastDraw, setLastDraw] = useState(null);
    const [date, setDate] = useState(null);

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

    function randomRotation(degree) {
        return Math.floor(Math.random() * 30) - degree; // Random number between -10 and 10
    }

    function randomCurve() {
        return `M10 10 Q${Math.random() * 70 + 10} ${Math.random() * 40 + 30} 90 10`;
    }

    return (
        <div className="lotto-container">
            <div className="lotto-box">
                <div className="lotto-title-container">
                    <div className="lotto-title">Letzte Ziehung vom:</div>
                    <div className="lotto-draw-date">{date}</div>
                </div>
                <div className="lotto-grid">
                    {[...Array(49)].map((_, i) => {
                        const isDrawn = lastDraw && includesNumber(lastDraw.lotto_numbers[0], i + 1);
                        const rotationBefore = isDrawn ? randomRotation(90) : 0;
                        const rotationAfter = isDrawn ? randomRotation(180) : 0;

                        return (
                            <div key={i} className={`lotto-number ${isDrawn ? 'drawn' : ''}`}>
                                {i + 1}
                                {isDrawn && (
                                    <>
                                        <svg className="x-line" style={{ transform: `rotate(${rotationBefore}deg)` }}>
                                            <path d={randomCurve()} stroke="currentColor" fill="none" strokeWidth="4" />
                                        </svg>
                                        <svg className="x-line" style={{ transform: `rotate(${rotationAfter}deg)` }}>
                                            <path d={randomCurve()} stroke="currentColor" fill="none" strokeWidth="4" />
                                        </svg>
                                    </>
                                )}
                            </div>
                        );
                    })}
                </div>
            </div>
        </div>
    );
}

export default LastDraw;
