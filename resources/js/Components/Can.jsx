import { useCan } from "@/lib/can";

export default function Can({ permission, children, fallback = null }) {
    return useCan(permission) ? children : fallback;
}
