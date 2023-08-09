import React, { useEffect, useState } from 'react';
import axios from 'axios';

function Generate() {
    const [numbers, setNumbers] = useState([]);

    useEffect(() => {
        // simulate getting the last drawn numbers from your server
        axios.get('/api/last-drawn-numbers')
            .then(response => {
                setNumbers(response.data);
            })
            .catch(error => {
                console.error('Error fetching last drawn numbers:', error);
            });
    }, []);

    return (
        <div>
            <h2>Letzte gezogene Zahlen:</h2>
            <div className="lotto-grid">
                {[...Array(49)].map((_, i) => (
                    <div key={i} className={`lotto-number ${numbers.includes(i + 1) ? 'drawn' : ''}`}>
                        {i + 1}
                    </div>
                ))}
            </div>
        </div>
    );
}

export default Generate;
