import { Injectable } from '@angular/core';
@Injectable({
    providedIn: 'root',
})
export class TokenService {

    constructor() { }

    handleData(token: any) {
        localStorage.setItem('auth_token', token);
    }

    getToken() {
        return localStorage.getItem('auth_token');
    }

    // Verify the token
    isValidToken() {
        const token = this.getToken();

        return token ? true : false;
    }

    // User state based on valid token
    isLoggedIn() {
        return this.isValidToken();
    }

    // Remove token
    removeToken() {
        localStorage.removeItem('auth_token');
    }
}