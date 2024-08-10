// ./assets/js/components/ExchangeRates/ExchangeRates.tsx

import './styles.css';

import React, {Component} from 'react';
import axios from 'axios';

export default class ExchangeRates extends Component {

    TRY_GET_REQUEST_DATA_COUNT = 7;
    BASE_URL = 'http://telemedi-zadanie.localhost';
    EARLIEST_DATE_POSSIBLE = '2023-01-01';

    constructor(props) {
        super(props);

        this.firstAvaliableRates = Object;
        this.firstAvaliableRatesDate = new Date();

        this.exchangeRatesTableRender = String;

        this.historicalExchangeRates = Object;

        this.getFirstAvailableRatesTrys = 0;
        this.state = {loading: true, dateInputValue: this.formatDateToString(this.firstAvaliableRatesDate)};
        this.message = '';
    }

    async getFirstAvailableRates() {
        let result = await axios.get(
            this.BASE_URL + `/api/exchange-rates`,
            {
                params: {
                    date: this.formatDateToString(this.firstAvaliableRatesDate)
                }
            }
        ).then(response => {
            if (typeof response.data.date === 'undefined') {
                return false;
            }

            return response.data;
        }).catch(error => {
            console.log(error)

            return false;
        });

        if (result === false) {
            this.firstAvaliableRatesDate.setDate(
                this.firstAvaliableRatesDate.getDate() - 1
            );

            this.getFirstAvailableRatesTrys++;

            this.getFirstAvailableRates();

            return;
        }

        this.firstAvaliableRates = result;

        this.prepareExchangeRatesTable();

        this.setState({loading: false, dateInputValue: result.date});

        if (this.getFirstAvailableRatesTrys !== 0) {
            this.message = 'Nie udało się pobrać danych na dzień dzisiejszy. Pobrano dane na dzień ' + this.state.dateInputValue;
        }
    }

    prepareExchangeRatesTable() {
        let isHistoricalData = typeof this.historicalExchangeRates.date !== 'undefined';

        let rows = [];

        if (typeof this.firstAvaliableRates.buyableCurrencies !== 'undefined') {
            this.firstAvaliableRates.buyableCurrencies.forEach((currency, index) => {
                rows.push((<tr key={'bc -' + index}>
                    <td>{currency.code}</td>
                    <td>{currency.name}</td>
                    <td id="exchange-rates-table-cell-left-border">{currency.nbpMidRate}</td>
                    <td>{currency.buyPrice}</td>
                    <td>{currency.sellPrice}</td>
                    {isHistoricalData
                        ? (<>
                            <td id="exchange-rates-table-cell-left-border">{this.historicalExchangeRates.buyableCurrencies[index].nbpMidRate}</td>
                            <td>{this.historicalExchangeRates.buyableCurrencies[index].buyPrice}</td>
                            <td>{this.historicalExchangeRates.buyableCurrencies[index].sellPrice}</td>

                        </>) : (<></>)
                    }
                </tr>))
            });
        }

        if (typeof this.firstAvaliableRates.supportedCurrencies !== 'undefined') {
            this.firstAvaliableRates.supportedCurrencies.forEach((currency, index) => {
                rows.push((<tr key={'sc -' + index}>
                    <td>{currency.code}</td>
                    <td>{currency.name}</td>
                    <td id="exchange-rates-table-cell-left-border">{currency.nbpMidRate}</td>
                    <td>-</td>
                    <td>{currency.sellPrice}</td>
                    {isHistoricalData
                        ? (<>
                            <td id="exchange-rates-table-cell-left-border">{this.historicalExchangeRates.supportedCurrencies[index].nbpMidRate}</td>
                            <td>-</td>
                            <td>{this.historicalExchangeRates.supportedCurrencies[index].sellPrice}</td>

                        </>) : (<></>)
                    }
                </tr>));
            });
        }

        this.exchangeRatesTableRender = (
            <table className="exchange-rates-table text-center">
                <thead>
                <tr>
                    <th colSpan={2}></th>
                    <th colSpan={3} id="exchange-rates-table-cell-left-border">
                        {this.firstAvaliableRates.date}
                    </th>
                    {isHistoricalData
                        ? (
                            <th colSpan={3} id="exchange-rates-table-cell-left-border">
                                {this.historicalExchangeRates.date}
                            </th>
                        ) : (<></>)
                    }
                </tr>
                </thead>
                <thead>
                <tr>
                    <th>Kod waluty</th>
                    <th>Nazwa</th>
                    <th id="exchange-rates-table-cell-left-border">Kurs NBP</th>
                    <th>Cena kupna</th>
                    <th>Cena sprzedaży</th>
                    {isHistoricalData
                        ? (
                            <>
                                <th id="exchange-rates-table-cell-left-border">Kurs NBP</th>
                                <th>Cena kupna</th>
                                <th>Cena sprzedaży</th>
                            </>
                        ) : (<></>)
                    }
                </tr>
                </thead>

                <tbody>
                {rows}
                </tbody>

            </table>
        );
    }

    formatDateToString(date = Date) {
        return date.toISOString().split('T')[0];
    }

    setBrowserUrlDateQueryParam(date = String | null) {
        if (history.pushState) {
            let newUrl = window.location.protocol + "//" + window.location.host + window.location.pathname
                + (date ? ('?date=' + date) : (''));
            window.history.pushState({path: newUrl}, '', newUrl);
        }
    }

    async getExchangeRatesHistoricalData(date = String) {
        this.setState({loading: true, dateInputValue: this.state.dateInputValue});

        this.message = '';

        if (this.validateDateInputValue(date) === false) {
            this.setState({loading: false, dateInputValue: this.state.dateInputValue});

            return;
        }

        let result = await axios.get(
            this.BASE_URL + `/api/exchange-rates`,
            {
                params: {
                    date: date
                }
            }
        ).then(response => {
            if (typeof response.data.date === 'undefined') {
                this.message = 'Błąd danych';

                return false;
            }

            return response.data;
        }).catch(error => {
            console.log(error)

            this.message = 'Błąd pobrania danych na dzień ' + date;

            return false;
        });

        if (result === false) {
            this.setState({loading: false, dateInputValue: this.historicalExchangeRates.date});

            return;
        }

        this.historicalExchangeRates = result;

        this.prepareExchangeRatesTable();

        this.setBrowserUrlDateQueryParam(this.historicalExchangeRates.date);

        this.setState({loading: false, dateInputValue: date});
    }

    validateDateInputValue(date) {
        let firstAvailableRatesDateString = this.formatDateToString(this.firstAvaliableRatesDate);

        if (date > firstAvailableRatesDateString) {
            this.message = 'Wybrana data nie może być późniejsza niż ' + firstAvailableRatesDateString;

            return false;
        }

        if (date < this.EARLIEST_DATE_POSSIBLE) {
            this.message = 'Wybrana data nie może być wcześniejsza niż ' + this.EARLIEST_DATE_POSSIBLE;

            return false;
        }

        return true;
    }

    componentDidMount() {
        this.getFirstAvailableRates();

        let querySearch = this.props.location.search;

        if (typeof querySearch !== 'string' || querySearch === '') {
            return;
        }

        const regex = /date=(\d{4}-\d{2}-\d{2})/gm;

        let resultDate = regex.exec(querySearch)

        if (! resultDate) {
            this.setBrowserUrlDateQueryParam(null);

            return;
        }

        if (typeof resultDate[1] === 'undefined') {
            this.setBrowserUrlDateQueryParam(null);

            return;
        }

        this.getExchangeRatesHistoricalData(resultDate[1]);
    }

    render() {
        const loading = this.state.loading;

        let thisHandler = this;

        function changeExchangeRatesDate(event) {
            thisHandler.getExchangeRatesHistoricalData(event.target.value);
        }

        return (
            <div className="exchange-rates-container">
                <div className="exchange-rates-container-row">
                    <h1 className="exchange-rates-container-title">Tabela kursów</h1>
                </div>
                <div className="exchange-rates-container-row">
                    <div className="exchange-rates-container-message">
                        {this.message}
                    </div>
                </div>
                <div className="exchange-rates-container-row">
                    <div>
                        {
                            loading
                                ? (
                                    <div className={'text-center'}>
                                        <span className="fa fa-spin fa-spinner fa-4x"></span>
                                    </div>
                                ) : (
                                    <div>
                                        <div className="exchange-rates-container-row">
                                            Wybierz date historyczną do porównania <input type="date" id="exchangeRatesDate"
                                                                                          name="exchangeRatesDate"
                                                                                          onChange={changeExchangeRatesDate}
                                                                                          value={this.state.dateInputValue}
                                        />
                                        </div>

                                        <div>
                                            {this.exchangeRatesTableRender}
                                        </div>
                                    </div>
                                )
                        }
                    </div>
                </div>
            </div>
        );
    }
}