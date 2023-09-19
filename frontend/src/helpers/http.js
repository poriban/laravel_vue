import axios from "axios"

// HTTP通信の関数
const http = () => {
    let options = {
        baseURL: 'http://localhost',
        headers: {}
    }

    if (localStorage.getItem('token')) {
        options.headers.Authorization = `Bearer ${localStorage.getItem('token')}`
    }

    return axios.create(options)
}

export default http