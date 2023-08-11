import React, {useEffect} from 'react';

function OptionsMenu(props) {
    const {sliders, onSliderChange, onConfirm} = props;

    const handleSliderChange = (name) => (e) => {
        const value = e.target.value;
        const fillPercentage = (value / 100) * 100;
        const red = value < 50 ? 255 : Math.floor(255 - (value - 50) * 5.1);
        const green = value > 50 ? 255 : Math.floor(value * 5.1);
        const blue = 0;


        e.target.style.background = `linear-gradient(to right, rgb(${red},${green},${blue}) ${fillPercentage}%, #ccc ${fillPercentage}%)`;

        onSliderChange(name, parseFloat(value));
    };

    useEffect(() => {
        for (const name in sliders) {
            const element = document.querySelector(`input[name="${name}"]`);
            const value = sliders[name];
            const fillPercentage = (value / 100) * 100;
            const red = value < 50 ? 255 : Math.floor(255 - (value - 50) * 5.1);
            const green = value > 50 ? 255 : Math.floor(value * 5.1);
            const blue = 0;

            if (element) { // Überprüfe, ob das Element existiert
                element.style.background = `linear-gradient(to right, rgb(${red},${green},${blue}) ${fillPercentage}%, #ccc ${fillPercentage}%)`;
            }
        }
    }, [sliders]);



    return (
        <div className="options-overlay">
            <div className="options-menu">
                <div className="options-menu-header">Einstellungen</div>
                <div className="slider-container">
                    <div className="slider-item">
                        <label>Gewichtung häufigste Zahlen:</label>
                        <input type="range" name="frequent" min="0" max="100" value={sliders.frequent}
                               onChange={handleSliderChange('frequent')}/>
                    </div>
                    <div className="slider-item">
                        <label>Gewichtung seltenste Zahlen:</label>
                        <input type="range" name="rare" min="0" max="100" value={sliders.rare}
                               onChange={handleSliderChange('rare')}/>
                    </div>
                    <div className="slider-item">
                        <label>Gewichtung längste Abwesenheit:</label>
                        <input type="range" name="longestAbsence" min="0" max="100" value={sliders.longestAbsence}
                               onChange={handleSliderChange('longestAbsence')}/>
                    </div>
                    <div className="slider-item">
                        <label>Gewichtung häufigste Paare:</label>
                        <input type="range" name="frequentPairs" min="0" max="100" value={sliders.frequentPairs}
                               onChange={handleSliderChange('frequentPairs')}/>
                    </div>
                </div>
                <input type="hidden" name="csrftoken" value="KbyUmhTLMpYj7CD2di7JKP1P3qmLlkPt" />
                <button className="confirm-button" onClick={onConfirm}>Bestätigen</button>

            </div>
        </div>
    );
}

export default OptionsMenu;
