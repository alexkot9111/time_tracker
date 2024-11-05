import { instance } from "./ApiConfig";

const AuthService = {
    login(email: string, password: string) {
        return instance.post("/api/login", { email, password });
    },

    refreshToken() {
        return instance.get("/api/token/refresh");
    },

    logout() {
        return instance.get("/api/logout");
    }
};

export default AuthService;