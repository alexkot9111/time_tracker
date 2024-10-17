import axios from "axios";

// Create an axios instance where cookies will be attached to each request
export const instance = axios.create({
    withCredentials: true,
    baseURL: "http://localhost:8888/",
});

// Request interceptor to add the access token from localStorage to each request
instance.interceptors.request.use(
    (config) => {
        config.headers.Authorization = `Bearer ${localStorage.getItem("token")}`;
        return config;
    }
);

// Response interceptor to handle invalid access tokens by refreshing them
let refreshTokenInProgress = false; // Flag to prevent multiple refresh requests

// Response interceptor to handle invalid access tokens by refreshing them
instance.interceptors.response.use(
    (config) => {
        return config; // If access token is valid, do nothing
    },
    async (error) => {
        const originalRequest = error.config;

        // Check if the error is due to an invalid access token and if a refresh is not in progress
        if (error.response.status === 401 && !refreshTokenInProgress) {
            refreshTokenInProgress = true; // Set the flag to true to indicate refresh in progress

            try {
                // Request to refresh the tokens
                const resp = await instance.get("/api/token/refresh");
                localStorage.setItem("token", resp.data.access_token);

                // Update the Authorization header in the original request
                originalRequest.headers.Authorization = `Bearer ${resp.data.access_token}`;

                // Resend the original request with the updated access token
                return instance(originalRequest);
            } catch (refreshError) {
                console.log("AUTH ERROR");
                throw refreshError; // Handle refresh token errors
            } finally {
                refreshTokenInProgress = false; // Reset the flag after processing
            }
        }

        // If the error is not due to authorization or refresh, throw it
        throw error;
    }
);


