import React, { useEffect, useState } from 'react';
import axios from 'axios'; // library for making HTTP requests
import { Link } from 'react-router-dom';

function Welcome() {
    const [setSelectedNumbers] = useState(null);

    useEffect(() => {
        // generate a unique ID
        const uniqueId = Math.random().toString(36).substring(2, 12);

        // make a request to your Laravel server to calculate the lotto numbers
        axios.get('/api/calculate-lotto-numbers', { params: { uniqueId: uniqueId } })
            .then(response => {
                setSelectedNumbers(response.data);
            })
            .catch(error => {
                console.error('Error calculating lotto numbers:', error);
            });
    }, []);

    return (
        <div className="Welcome">
            <Link to="/last-draw" className="Welcome-header">
                Dein 6´er im Lotto
            </Link>
        </div>
    );
}

export default Welcome;
