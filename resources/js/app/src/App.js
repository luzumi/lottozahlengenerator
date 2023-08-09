import React from 'react';
import { BrowserRouter as Router, Routes, Route } from 'react-router-dom';
import Welcome from './Components/Welcome';
import Generate from './Components/Generate';
import LastDraw from './Components/LastDraw';
import './App.css';

function App() {
    return (
        <Router>
            <div className="App">
                <Routes>
                    <Route path="/" element={<Welcome />} />
                    <Route path="/generate" element={<Generate />} />
                    <Route path="/last-draw" element={<LastDraw />} />
                </Routes>
            </div>
        </Router>
    );
}

export default App;
